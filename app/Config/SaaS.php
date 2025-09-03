<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class SaaS extends BaseConfig
{
    /**
     * Tenancy mode: subdomain, path, header
     */
    public string $tenancy = 'subdomain';

    /**
     * Tenant header name (used when tenancy mode is 'header')
     */
    public string $tenantHeader = 'X-TENANT-ID';

    /**
     * Default tenant database prefix
     */
    public string $tenantDbPrefix = 'tenant_';

    /**
     * Enable tenant isolation
     */
    public bool $enableIsolation = true;

    /**
     * Tenant onboarding settings
     */
    public array $onboarding = [
        'enabled' => true,
        'redirectUrl' => '/onboarding/choose-tenant',
        'allowedDomains' => ['localhost', '127.0.0.1']
    ];

    /**
     * Tenant settings
     */
    public array $defaultSettings = [
        'theme' => 'default',
        'timezone' => 'Asia/Jakarta',
        'currency' => 'IDR',
        'language' => 'id',
        'dateFormat' => 'd/m/Y',
        'timeFormat' => 'H:i'
    ];

    /**
     * Tenant status options
     */
    public array $statusOptions = [
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif',
        'suspended' => 'Ditangguhkan'
    ];

    /**
     * Tenant limits
     */
    public array $limits = [
        'maxUsers' => 100,
        'maxStorage' => '1GB',
        'maxDomains' => 5
    ];
}
