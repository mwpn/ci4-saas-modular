<?php

namespace App\Middleware;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class TenantIsolationMiddleware implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Resolve tenant dari request
        $tenantService = service('tenant');
        $tenantId = $tenantService->id();

        // Jika tidak ada tenant ID, redirect ke halaman pilih tenant
        if (!$tenantId) {
            return redirect()->to('/onboarding/choose-tenant');
        }

        // Validasi tenant apakah ada di database dan aktif
        $tenantModel = new \Modules\Core\Infrastructure\Models\TenantModel();
        $tenant = $tenantModel->where('slug', $tenantId)->first();

        if (!$tenant) {
            return redirect()->to('/onboarding/choose-tenant')->with('error', 'Tenant tidak ditemukan');
        }

        if (!$tenant->isActive()) {
            return redirect()->to('/onboarding/choose-tenant')->with('error', 'Tenant tidak aktif');
        }

        // Set tenant context untuk digunakan di seluruh aplikasi
        $this->setTenantContext($tenant);

        // Set database connection jika menggunakan database per tenant
        $this->setTenantDatabase($tenant);

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Cleanup tenant context setelah request selesai
        $this->clearTenantContext();

        return $response;
    }

    /**
     * Set tenant context untuk digunakan di seluruh aplikasi
     */
    private function setTenantContext($tenant): void
    {
        // Simpan tenant data di session
        session()->set('tenant_id', $tenant->id);
        session()->set('tenant_slug', $tenant->slug);
        session()->set('tenant_name', $tenant->name);
        session()->set('tenant_settings', $tenant->getSettingsArray());

        // Set global tenant context
        if (!defined('TENANT_ID')) {
            define('TENANT_ID', $tenant->id);
        }
        if (!defined('TENANT_SLUG')) {
            define('TENANT_SLUG', $tenant->slug);
        }
    }

    /**
     * Set database connection untuk tenant jika diperlukan
     */
    private function setTenantDatabase($tenant): void
    {
        // Jika menggunakan database per tenant
        if (env('saas.databasePerTenant', false) && !empty($tenant->database_name)) {
            $dbConfig = config('Database');
            $dbConfig->default['database'] = $tenant->database_name;

            // Reconnect dengan database tenant
            $db = \Config\Database::connect();
            $db->reconnect();
        }
    }

    /**
     * Clear tenant context
     */
    private function clearTenantContext(): void
    {
        // Clear session tenant data
        session()->remove(['tenant_id', 'tenant_slug', 'tenant_name', 'tenant_settings']);
    }
}
