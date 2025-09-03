<?php

namespace App\Services;

class TenantContext
{
    protected static ?object $tenant = null;
    protected static ?string $tenantId = null;
    protected static ?string $tenantSlug = null;
    protected static array $settings = [];

    /**
     * Set tenant context
     */
    public static function setTenant(object $tenant): void
    {
        self::$tenant = $tenant;
        self::$tenantId = $tenant->id;
        self::$tenantSlug = $tenant->slug;
        self::$settings = $tenant->getSettingsArray();

        // Set session data
        session()->set([
            'tenant_id' => $tenant->id,
            'tenant_slug' => $tenant->slug,
            'tenant_name' => $tenant->name,
            'tenant_settings' => $tenant->getSettingsArray()
        ]);
    }

    /**
     * Get current tenant
     */
    public static function getTenant(): ?object
    {
        if (self::$tenant === null) {
            // Try to get from session
            $tenantId = session('tenant_id');
            if ($tenantId) {
                $tenantModel = new \Modules\Core\Infrastructure\Models\TenantModel();
                $tenant = $tenantModel->find($tenantId);
                if ($tenant) {
                    self::setTenant($tenant);
                }
            }
        }
        return self::$tenant;
    }

    /**
     * Get tenant ID
     */
    public static function getTenantId(): ?string
    {
        if (self::$tenantId === null) {
            self::$tenantId = session('tenant_id');
        }
        return self::$tenantId;
    }

    /**
     * Get tenant slug
     */
    public static function getTenantSlug(): ?string
    {
        if (self::$tenantSlug === null) {
            self::$tenantSlug = session('tenant_slug');
        }
        return self::$tenantSlug;
    }

    /**
     * Get tenant settings
     */
    public static function getSettings(): array
    {
        if (empty(self::$settings)) {
            self::$settings = session('tenant_settings') ?? [];
        }
        return self::$settings;
    }

    /**
     * Get specific tenant setting
     */
    public static function getSetting(string $key, $default = null)
    {
        $settings = self::getSettings();
        return $settings[$key] ?? $default;
    }

    /**
     * Check if tenant context is set
     */
    public static function hasTenant(): bool
    {
        return self::getTenant() !== null;
    }

    /**
     * Clear tenant context
     */
    public static function clear(): void
    {
        self::$tenant = null;
        self::$tenantId = null;
        self::$tenantSlug = null;
        self::$settings = [];

        // Clear session data
        session()->remove(['tenant_id', 'tenant_slug', 'tenant_name', 'tenant_settings']);
    }

    /**
     * Get tenant name
     */
    public static function getTenantName(): ?string
    {
        $tenant = self::getTenant();
        return $tenant ? $tenant->name : null;
    }

    /**
     * Check if tenant is active
     */
    public static function isActive(): bool
    {
        $tenant = self::getTenant();
        return $tenant && $tenant->isActive();
    }

    /**
     * Get tenant database name
     */
    public static function getDatabaseName(): ?string
    {
        $tenant = self::getTenant();
        return $tenant ? $tenant->database_name : null;
    }

    /**
     * Refresh tenant context from database
     */
    public static function refresh(): void
    {
        $tenantId = self::getTenantId();
        if ($tenantId) {
            $tenantModel = new \Modules\Core\Infrastructure\Models\TenantModel();
            $tenant = $tenantModel->find($tenantId);
            if ($tenant) {
                self::setTenant($tenant);
            }
        }
    }
}
