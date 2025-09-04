<?php

namespace Modules\Core\Providers;

use CodeIgniter\Config\Services;
use Modules\Core\Application\Services\TenantApplicationService;
use Modules\Core\Infrastructure\Repositories\TenantRepository;

class ServiceProvider
{
    public function register(): void
    {
        // Bind Tenant Repository
        Services::injectMock('tenantRepository', function () {
            return new TenantRepository();
        });

        // Bind Tenant Application Service
        Services::injectMock('tenantApplicationService', function () {
            return new TenantApplicationService();
        });
    }

    public function boot(): void
    {
        // Boot logic here if needed
    }
}
