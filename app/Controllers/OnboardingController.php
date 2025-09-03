<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Modules\Core\Infrastructure\Models\TenantModel;

class OnboardingController extends BaseController
{
    /**
     * Choose tenant page
     */
    public function chooseTenant()
    {
        $tenantModel = new TenantModel();
        $tenants = $tenantModel->where('status', 'active')->findAll();

        $data = [
            'title' => 'Pilih Tenant',
            'tenants' => $tenants
        ];

        return view('onboarding/choose_tenant', $data);
    }

    /**
     * Set tenant
     */
    public function setTenant()
    {
        $tenantId = $this->request->getPost('tenant_id');

        if (!$tenantId) {
            return redirect()->back()->with('error', 'Tenant ID harus diisi');
        }

        $tenantModel = new TenantModel();
        $tenant = $tenantModel->find($tenantId);

        if (!$tenant) {
            return redirect()->back()->with('error', 'Tenant tidak ditemukan');
        }

        if (!$tenant->isActive()) {
            return redirect()->back()->with('error', 'Tenant tidak aktif');
        }

        // Set tenant context
        \App\Services\TenantContext::setTenant($tenant);

        // Redirect to dashboard
        return redirect()->to('/')->with('success', 'Tenant berhasil dipilih');
    }

    /**
     * Switch tenant
     */
    public function switchTenant()
    {
        return $this->chooseTenant();
    }
}
