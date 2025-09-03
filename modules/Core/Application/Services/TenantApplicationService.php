<?php

namespace Modules\Core\Application\Services;

use Modules\Core\Infrastructure\Repositories\TenantRepository;
use Modules\Core\Domain\Entities\Tenant;

class TenantApplicationService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new TenantRepository();
    }

    /**
     * Get all tenants
     */
    public function getAllTenants(): array
    {
        return $this->repository->getAll();
    }

    /**
     * Get tenant by ID
     */
    public function getTenantById(string $id): ?Tenant
    {
        return $this->repository->getById($id);
    }

    /**
     * Get tenant by slug
     */
    public function getTenantBySlug(string $slug): ?Tenant
    {
        return $this->repository->getBySlug($slug);
    }

    /**
     * Get tenant by domain
     */
    public function getTenantByDomain(string $domain): ?Tenant
    {
        return $this->repository->getByDomain($domain);
    }

    /**
     * Create new tenant
     */
    public function createTenant(array $data): ?int
    {
        // Validate required fields
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('Nama tenant harus diisi');
        }

        // Check if slug already exists
        if (isset($data['slug']) && $this->repository->getBySlug($data['slug'])) {
            throw new \InvalidArgumentException('Slug tenant sudah digunakan');
        }

        // Check if domain already exists
        if (isset($data['domain']) && $data['domain'] && $this->repository->getByDomain($data['domain'])) {
            throw new \InvalidArgumentException('Domain sudah digunakan');
        }

        return $this->repository->create($data);
    }

    /**
     * Update tenant
     */
    public function updateTenant(string $id, array $data): bool
    {
        $tenant = $this->repository->getById($id);
        if (!$tenant) {
            throw new \InvalidArgumentException('Tenant tidak ditemukan');
        }

        // Check if slug already exists (excluding current tenant)
        if (isset($data['slug']) && $data['slug'] !== $tenant->slug) {
            $existingTenant = $this->repository->getBySlug($data['slug']);
            if ($existingTenant && $existingTenant->id !== $id) {
                throw new \InvalidArgumentException('Slug tenant sudah digunakan');
            }
        }

        // Check if domain already exists (excluding current tenant)
        if (isset($data['domain']) && $data['domain'] !== $tenant->domain) {
            $existingTenant = $this->repository->getByDomain($data['domain']);
            if ($existingTenant && $existingTenant->id !== $id) {
                throw new \InvalidArgumentException('Domain sudah digunakan');
            }
        }

        return $this->repository->update($id, $data);
    }

    /**
     * Delete tenant
     */
    public function deleteTenant(string $id): bool
    {
        $tenant = $this->repository->getById($id);
        if (!$tenant) {
            throw new \InvalidArgumentException('Tenant tidak ditemukan');
        }

        return $this->repository->delete($id);
    }

    /**
     * Activate tenant
     */
    public function activateTenant(string $id): bool
    {
        return $this->repository->updateStatus($id, 'active');
    }

    /**
     * Deactivate tenant
     */
    public function deactivateTenant(string $id): bool
    {
        return $this->repository->updateStatus($id, 'inactive');
    }

    /**
     * Suspend tenant
     */
    public function suspendTenant(string $id): bool
    {
        return $this->repository->updateStatus($id, 'suspended');
    }

    /**
     * Get tenant settings
     */
    public function getTenantSettings(string $id): array
    {
        return $this->repository->getSettings($id);
    }

    /**
     * Update tenant settings
     */
    public function updateTenantSettings(string $id, array $settings): bool
    {
        $tenant = $this->repository->getById($id);
        if (!$tenant) {
            throw new \InvalidArgumentException('Tenant tidak ditemukan');
        }

        return $this->repository->updateSettings($id, $settings);
    }

    /**
     * Get tenant statistics
     */
    public function getTenantStats(): array
    {
        return $this->repository->getStats();
    }

    /**
     * Search tenants
     */
    public function searchTenants(string $query): array
    {
        return $this->repository->search($query);
    }

    /**
     * Get tenants with pagination
     */
    public function getTenantsPaginated(int $perPage = 10, int $page = 1): array
    {
        return $this->repository->getPaginated($perPage, $page);
    }

    /**
     * Validate tenant access
     */
    public function validateTenantAccess(string $tenantId): bool
    {
        $tenant = $this->repository->getById($tenantId);
        return $tenant && $tenant->status === 'active';
    }
}
