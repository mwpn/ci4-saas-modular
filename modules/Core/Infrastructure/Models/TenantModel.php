<?php

namespace Modules\Core\Infrastructure\Models;

use CodeIgniter\Model;
use Modules\Core\Domain\Entities\Tenant;
use Modules\Core\Infrastructure\Models\BaseModel;

class TenantModel extends BaseModel
{
    protected $table = 'tenants';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Tenant::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $tenantAware = false; // Tenant model tidak perlu tenant scope
    protected $allowedFields = [
        'name',
        'slug',
        'domain',
        'database_name',
        'status',
        'settings',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'slug' => 'required|min_length[3]|max_length[50]|is_unique[tenants.slug,id,{id}]',
        'domain' => 'permit_empty|valid_email|is_unique[tenants.domain,id,{id}]',
        'status' => 'required|in_list[active,inactive,suspended]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama tenant harus diisi',
            'min_length' => 'Nama tenant minimal 3 karakter',
            'max_length' => 'Nama tenant maksimal 100 karakter'
        ],
        'slug' => [
            'required' => 'Slug tenant harus diisi',
            'min_length' => 'Slug tenant minimal 3 karakter',
            'max_length' => 'Slug tenant maksimal 50 karakter',
            'is_unique' => 'Slug tenant sudah digunakan'
        ],
        'domain' => [
            'valid_email' => 'Format domain tidak valid',
            'is_unique' => 'Domain sudah digunakan'
        ],
        'status' => [
            'required' => 'Status tenant harus diisi',
            'in_list' => 'Status tenant tidak valid'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['beforeInsert'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['beforeUpdate'];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Before insert callback
     */
    protected function beforeInsert(array $data): array
    {
        if (!isset($data['data']['slug'])) {
            $data['data']['slug'] = $this->generateSlug($data['data']['name']);
        }

        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'active';
        }

        return $data;
    }

    /**
     * Before update callback
     */
    protected function beforeUpdate(array $data): array
    {
        if (isset($data['data']['name']) && !isset($data['data']['slug'])) {
            $data['data']['slug'] = $this->generateSlug($data['data']['name']);
        }

        return $data;
    }

    /**
     * Generate slug from name
     */
    protected function generateSlug(string $name): string
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        // Ensure uniqueness
        $originalSlug = $slug;
        $counter = 1;

        while ($this->where('slug', $slug)->first()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get tenant by slug
     */
    public function getBySlug(string $slug): ?Tenant
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Get tenant by domain
     */
    public function getByDomain(string $domain): ?Tenant
    {
        return $this->where('domain', $domain)->first();
    }

    /**
     * Get active tenants only
     */
    public function getActive(): array
    {
        return $this->where('status', 'active')->findAll();
    }

    /**
     * Check if tenant is active
     */
    public function isActive(string $tenantId): bool
    {
        $tenant = $this->find($tenantId);
        return $tenant && $tenant->status === 'active';
    }

    /**
     * Get tenant settings
     */
    public function getSettings(string $tenantId): array
    {
        $tenant = $this->find($tenantId);
        if (!$tenant) {
            return [];
        }

        return json_decode($tenant->settings ?? '{}', true);
    }

    /**
     * Update tenant settings
     */
    public function updateSettings(string $tenantId, array $settings): bool
    {
        return $this->update($tenantId, [
            'settings' => json_encode($settings)
        ]);
    }

    /**
     * Create new tenant
     */
    public function createTenant(array $data): ?int
    {
        $data['slug'] = $this->generateSlug($data['name']);
        $data['status'] = $data['status'] ?? 'active';
        $data['settings'] = json_encode($data['settings'] ?? []);

        if ($this->insert($data)) {
            return $this->getInsertID();
        }

        return null;
    }

    /**
     * Update tenant status
     */
    public function updateStatus(string $tenantId, string $status): bool
    {
        return $this->update($tenantId, ['status' => $status]);
    }

    /**
     * Get tenant statistics
     */
    public function getStats(): array
    {
        return [
            'total' => $this->countAllResults(),
            'active' => $this->where('status', 'active')->countAllResults(),
            'inactive' => $this->where('status', 'inactive')->countAllResults(),
            'suspended' => $this->where('status', 'suspended')->countAllResults()
        ];
    }
}
