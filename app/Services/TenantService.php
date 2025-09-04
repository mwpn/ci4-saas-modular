<?php

namespace App\Services;

use CodeIgniter\Config\Services;
use Modules\Core\Infrastructure\Models\TenantModel;

class TenantService
{
    protected ?string $tenantId = null;
    protected ?object $tenant = null;
    protected string $tenancyMode;

    public function resolve(): void
    {
        $this->tenancyMode = env('saas.tenancy', 'subdomain');

        if ($this->tenancyMode === 'subdomain') {
            $host = Services::request()->getServer('HTTP_HOST');
            $segments = explode('.', $host);
            $this->tenantId = ($segments[0] ?? null) && $segments[0] !== 'www' ? $segments[0] : null;
        } elseif ($this->tenancyMode === 'path') {
            $segment = Services::request()->getUri()->getSegment(1);
            $this->tenantId = $segment ?: null;
        } else {
            $header = env('saas.tenantHeader', 'X-TENANT-ID');
            $this->tenantId = Services::request()->getHeaderLine($header) ?: null;
        }
    }

    public function id(): ?string
    {
        if ($this->tenantId === null) {
            $this->resolve();
        }
        return $this->tenantId;
    }

    public function getTenancyMode(): string
    {
        return $this->tenancyMode ?? env('saas.tenancy', 'subdomain');
    }

    /**
     * Get tenant object
     */
    public function getTenant(): ?object
    {
        if ($this->tenant === null && $this->id()) {
            $tenantModel = new TenantModel();
            $this->tenant = $tenantModel->where('slug', $this->id())->first();
        }
        return $this->tenant;
    }

    /**
     * Check if tenant exists and is active
     */
    public function isValid(): bool
    {
        $tenant = $this->getTenant();
        return $tenant && $tenant->isActive();
    }

    /**
     * Get tenant ID from session (fallback)
     */
    public function getSessionTenantId(): ?string
    {
        return session('tenant_id');
    }

    /**
     * Set tenant ID manually
     */
    public function setTenantId(string $tenantId): void
    {
        $this->tenantId = $tenantId;
        $this->tenant = null; // Reset tenant object
    }

    /**
     * Get tenant settings
     */
    public function getSettings(): array
    {
        $tenant = $this->getTenant();
        return $tenant ? $tenant->getSettingsArray() : [];
    }

    /**
     * Get specific tenant setting
     */
    public function getSetting(string $key, $default = null)
    {
        $tenant = $this->getTenant();
        return $tenant ? $tenant->getSetting($key, $default) : $default;
    }

    /**
     * Check if tenant isolation is enabled
     */
    public function isIsolationEnabled(): bool
    {
        return env('saas.enableIsolation', true);
    }

    /**
     * Get tenant database name
     */
    public function getDatabaseName(): ?string
    {
        $tenant = $this->getTenant();
        return $tenant ? $tenant->database_name : null;
    }

    /**
     * Clear tenant cache
     */
    public function clearCache(): void
    {
        $this->tenant = null;
    }
}
