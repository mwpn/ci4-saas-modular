# init-saas.ps1
$ErrorActionPreference = "Stop"

# 1) Folders
$dirs = @(
    "modules/Core/Domain/Entities",
    "modules/Core/Application",
    "modules/Core/Infrastructure/Models",
    "modules/Core/Infrastructure/Repositories",
    "modules/Core/Presentation/Controllers",
    "modules/Core/Config",
    "modules/Core/Database/Migrations",
    "modules/Core/Database/Seeds",
    "modules/Core/Database/Factories",
    "modules/Core/Providers",
    "app/Services",
    "app/Filters",
    "writable"
)
$dirs | ForEach-Object { New-Item -ItemType Directory -Force -Path $_ | Out-Null }

# 2) ServiceProvider
@"
<?php
namespace Modules\Core\Providers;

class ServiceProvider
{
    public function register(): void
    {
        // Bind service/repo disini jika perlu
    }
}
"@ | Set-Content -Encoding UTF8 "modules/Core/Providers/ServiceProvider.php"

# 3) Routes
@"
<?php
\$routes->group('', ['namespace' => 'Modules\\Core\\Presentation\\Controllers'], function(\$routes) {
    \$routes->get('/', 'HomeController::index');
});
"@ | Set-Content -Encoding UTF8 "modules/Core/Routes.php"

# 4) HomeController
@"
<?php
namespace Modules\Core\Presentation\Controllers;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        return 'Hello SaaS Modular!';
    }
}
"@ | Set-Content -Encoding UTF8 "modules/Core/Presentation/Controllers/HomeController.php"

# 5) BaseEntity
@"
<?php
namespace Modules\Core\Domain\Entities;

use CodeIgniter\Entity\Entity;

class BaseEntity extends Entity {}
"@ | Set-Content -Encoding UTF8 "modules/Core/Domain/Entities/BaseEntity.php"

# 6) BaseModel (tenant-aware)
@"
<?php
namespace Modules\Core\Infrastructure\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    protected \$useTimestamps = true;
    protected \$tenantField   = 'tenant_id';
    protected \$tenantAware   = true; // set false kalau tabel global

    protected function applyTenantScope()
    {
        if (! \$this->tenantAware) return \$this;
        \$tenantId = service('tenant')->id();
        if (\$tenantId) {
            \$this->where(\$this->table . '.' . \$this->tenantField, \$tenantId);
        }
        return \$this;
    }

    public function where(\$key = null, \$value = null, bool \$escape = null)
    {
        \$this->applyTenantScope();
        return parent::where(\$key, \$value, \$escape);
    }

    public function insert(\$data = null, bool \$returnID = true)
    {
        if (\$this->tenantAware && is_array(\$data) && ! isset(\$data[\$this->tenantField])) {
            \$data[\$this->tenantField] = service('tenant')->id();
        }
        return parent::insert(\$data, \$returnID);
    }
}
"@ | Set-Content -Encoding UTF8 "modules/Core/Infrastructure/Models/BaseModel.php"

# 7) TenantService
@"
<?php
namespace App\Services;

use CodeIgniter\Config\Services;

class TenantService
{
    protected ?string \$tenantId = null;

    public function resolve(): void
    {
        \$mode = env('saas.tenancy', 'subdomain');
        if (\$mode === 'subdomain') {
            \$host = Services::request()->getServer('HTTP_HOST');
            \$parts = explode('.', \$host);
            \$this->tenantId = (\$parts[0] ?? null) && \$parts[0] !== 'www' ? \$parts[0] : null;
        } elseif (\$mode === 'path') {
            \$seg = Services::request()->uri->getSegment(1);
            \$this->tenantId = \$seg ?: null;
        } else {
            \$header = env('saas.tenantHeader', 'X-TENANT-ID');
            \$this->tenantId = Services::request()->getHeaderLine(\$header) ?: null;
        }
    }

    public function id(): ?string
    {
        if (\$this->tenantId === null) \$this->resolve();
        return \$this->tenantId;
    }
}
"@ | Set-Content -Encoding UTF8 "app/Services/TenantService.php"

# 8) TenantFilter
@"
<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class TenantFilter implements FilterInterface
{
    public function before(RequestInterface \$request, \$arguments = null)
    {
        if (! service('tenant')->id()) {
            return redirect()->to('/onboarding/choose-tenant');
        }
    }

    public function after(RequestInterface \$request, ResponseInterface \$response, \$arguments = null) {}
}
"@ | Set-Content -Encoding UTF8 "app/Filters/TenantFilter.php"

# 9) Services extender (register tenant())
(Get-Content "app/Config/Services.php") `
    -replace "\}\s*\Z", @"
    public static function tenant(\$getShared = true)
    {
        if (\$getShared) return static::getSharedInstance('tenant');
        return new \App\Services\TenantService();
    }
}
"@ | Set-Content -Encoding UTF8 "app/Config/Services.php"

# 10) Tambah alias tenant secara aman
$filtersFile = "app/Config/Filters.php"
(Get-Content $filtersFile) |
ForEach-Object {
    if ($_ -match "public \$aliases = \[") {
        $_
        "    'tenant' => \App\Filters\TenantFilter::class,"
    }
    else {
        $_
    }
} | Set-Content -Encoding UTF8 $filtersFile


# 11) Bootstrap module loader di Events.php (replace)
@"
<?php
namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Events\EventsConfig;

Events::on('pre_system', static function () {
    \$modulesPath = ROOTPATH . 'modules';
    foreach (glob(\$modulesPath . '/*/Providers/ServiceProvider.php') as \$provider) {
        require_once \$provider;
        \$ns = 'Modules\\\\' . basename(dirname(dirname(\$provider))) . '\\\\Providers\\\\ServiceProvider';
        if (class_exists(\$ns)) {
            (new \$ns())->register();
        }
    }
});

Events::on('pre_controller', static function () {
    \$modulesPath = ROOTPATH . 'modules';
    \$routes = service('routes');
    foreach (glob(\$modulesPath . '/*/Routes.php') as \$routeFile) {
        require \$routeFile;
    }
});

/* Tambah event custom di sini bila perlu */
"@ | Set-Content -Encoding UTF8 "app/Config/Events.php"

# 12) Git keep
New-Item -ItemType File -Force -Path "writable/.gitkeep" | Out-Null

Write-Host "Done. Starter minimal terpasang. Jalankan: php spark serve" -ForegroundColor Green
