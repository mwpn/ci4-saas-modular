<?php

namespace Modules\Core\Domain\Entities;

use Modules\Core\Domain\Entities\BaseEntity;

class Tenant extends BaseEntity
{
    protected $table = 'tenants';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
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
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Check if tenant is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get tenant settings as array
     */
    public function getSettingsArray(): array
    {
        return json_decode($this->settings ?? '{}', true);
    }

    /**
     * Set tenant settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = json_encode($settings);
    }

    /**
     * Get a specific setting value
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->getSettingsArray();
        return $settings[$key] ?? $default;
    }

    /**
     * Set a specific setting value
     */
    public function setSetting(string $key, $value): void
    {
        $settings = $this->getSettingsArray();
        $settings[$key] = $value;
        $this->setSettings($settings);
    }
}
