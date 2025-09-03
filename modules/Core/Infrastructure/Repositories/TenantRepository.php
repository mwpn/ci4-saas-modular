<?php

namespace Modules\Core\Infrastructure\Repositories;

use Modules\Core\Infrastructure\Models\TenantModel;
use Modules\Core\Domain\Entities\Tenant;

class TenantRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new TenantModel();
    }

    /**
     * Get all tenants
     */
    public function getAll(): array
    {
        return $this->model->findAll();
    }

    /**
     * Get tenant by ID
     */
    public function getById(string $id): ?Tenant
    {
        return $this->model->find($id);
    }

    /**
     * Get tenant by slug
     */
    public function getBySlug(string $slug): ?Tenant
    {
        return $this->model->getBySlug($slug);
    }

    /**
     * Get tenant by domain
     */
    public function getByDomain(string $domain): ?Tenant
    {
        return $this->model->getByDomain($domain);
    }

    /**
     * Get active tenants
     */
    public function getActive(): array
    {
        return $this->model->getActive();
    }

    /**
     * Create new tenant
     */
    public function create(array $data): ?int
    {
        return $this->model->createTenant($data);
    }

    /**
     * Update tenant
     */
    public function update(string $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    /**
     * Delete tenant
     */
    public function delete(string $id): bool
    {
        return $this->model->delete($id);
    }

    /**
     * Check if tenant is active
     */
    public function isActive(string $id): bool
    {
        return $this->model->isActive($id);
    }

    /**
     * Get tenant settings
     */
    public function getSettings(string $id): array
    {
        return $this->model->getSettings($id);
    }

    /**
     * Update tenant settings
     */
    public function updateSettings(string $id, array $settings): bool
    {
        return $this->model->updateSettings($id, $settings);
    }

    /**
     * Update tenant status
     */
    public function updateStatus(string $id, string $status): bool
    {
        return $this->model->updateStatus($id, $status);
    }

    /**
     * Get tenant statistics
     */
    public function getStats(): array
    {
        return $this->model->getStats();
    }

    /**
     * Search tenants
     */
    public function search(string $query): array
    {
        return $this->model->groupStart()
            ->like('name', $query)
            ->orLike('slug', $query)
            ->orLike('domain', $query)
            ->groupEnd()
            ->findAll();
    }

    /**
     * Get tenants with pagination
     */
    public function getPaginated(int $perPage = 10, int $page = 1): array
    {
        return $this->model->paginate($perPage, 'default', $page);
    }
}
