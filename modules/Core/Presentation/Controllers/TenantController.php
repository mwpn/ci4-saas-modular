<?php

namespace Modules\Core\Presentation\Controllers;

use App\Controllers\BaseController;
use Modules\Core\Application\Services\TenantApplicationService;

class TenantController extends BaseController
{
    protected $tenantService;

    public function __construct()
    {
        $this->tenantService = new TenantApplicationService();
    }

    /**
     * Display a listing of tenants
     */
    public function index()
    {
        $data = [
            'title' => 'Daftar Tenants',
            'tenants' => $this->tenantService->getAllTenants(),
            'stats' => $this->tenantService->getTenantStats()
        ];

        return view('Modules\Core\Views\tenants\index', $data);
    }

    /**
     * Show the form for creating a new tenant
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Tenant Baru'
        ];

        return view('Modules\Core\Views\tenants\create', $data);
    }

    /**
     * Store a newly created tenant
     */
    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'domain' => 'permit_empty|valid_email',
            'status' => 'required|in_list[active,inactive,suspended]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'name' => $this->request->getPost('name'),
                'domain' => $this->request->getPost('domain'),
                'database_name' => $this->request->getPost('database_name'),
                'status' => $this->request->getPost('status'),
                'settings' => json_decode($this->request->getPost('settings') ?? '{}', true)
            ];

            $tenantId = $this->tenantService->createTenant($data);

            if ($tenantId) {
                return redirect()->to('/tenants')->with('success', 'Tenant berhasil dibuat');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal membuat tenant');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified tenant
     */
    public function show($id)
    {
        $tenant = $this->tenantService->getTenantById($id);

        if (!$tenant) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Tenant tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Tenant: ' . $tenant->name,
            'tenant' => $tenant,
            'settings' => $this->tenantService->getTenantSettings($id)
        ];

        return view('Modules\Core\Views\tenants\show', $data);
    }

    /**
     * Show the form for editing the specified tenant
     */
    public function edit($id)
    {
        $tenant = $this->tenantService->getTenantById($id);

        if (!$tenant) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Tenant tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Tenant: ' . $tenant->name,
            'tenant' => $tenant
        ];

        return view('Modules\Core\Views\tenants\edit', $data);
    }

    /**
     * Update the specified tenant
     */
    public function update($id)
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'domain' => 'permit_empty|valid_email',
            'status' => 'required|in_list[active,inactive,suspended]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'name' => $this->request->getPost('name'),
                'domain' => $this->request->getPost('domain'),
                'database_name' => $this->request->getPost('database_name'),
                'status' => $this->request->getPost('status'),
                'settings' => json_decode($this->request->getPost('settings') ?? '{}', true)
            ];

            $success = $this->tenantService->updateTenant($id, $data);

            if ($success) {
                return redirect()->to('/tenants')->with('success', 'Tenant berhasil diperbarui');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui tenant');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified tenant
     */
    public function delete($id)
    {
        try {
            $success = $this->tenantService->deleteTenant($id);

            if ($success) {
                return redirect()->to('/tenants')->with('success', 'Tenant berhasil dihapus');
            } else {
                return redirect()->back()->with('error', 'Gagal menghapus tenant');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Activate tenant
     */
    public function activate($id)
    {
        try {
            $success = $this->tenantService->activateTenant($id);

            if ($success) {
                return redirect()->back()->with('success', 'Tenant berhasil diaktifkan');
            } else {
                return redirect()->back()->with('error', 'Gagal mengaktifkan tenant');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Deactivate tenant
     */
    public function deactivate($id)
    {
        try {
            $success = $this->tenantService->deactivateTenant($id);

            if ($success) {
                return redirect()->back()->with('success', 'Tenant berhasil dinonaktifkan');
            } else {
                return redirect()->back()->with('error', 'Gagal menonaktifkan tenant');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Suspend tenant
     */
    public function suspend($id)
    {
        try {
            $success = $this->tenantService->suspendTenant($id);

            if ($success) {
                return redirect()->back()->with('success', 'Tenant berhasil ditangguhkan');
            } else {
                return redirect()->back()->with('error', 'Gagal menangguhkan tenant');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
