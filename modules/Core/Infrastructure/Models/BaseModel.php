<?php

namespace Modules\Core\Infrastructure\Models;

use CodeIgniter\Model;
use App\Services\TenantContext;

class BaseModel extends Model
{
    protected $tenantAware = true;
    protected $tenantField = 'tenant_id';
    protected $autoTenantScope = true; // set false kalau tabel global
    protected $tenantScopeApplied = false;

    /**
     * Apply tenant scope to queries
     */
    protected function applyTenantScope()
    {
        if (!$this->tenantAware || !$this->autoTenantScope || $this->tenantScopeApplied) {
            return $this;
        }

        $tenantId = $this->getTenantId();
        if ($tenantId) {
            $this->where($this->table . '.' . $this->tenantField, $tenantId);
            $this->tenantScopeApplied = true;
        }

        return $this;
    }

    /**
     * Get tenant ID from context
     */
    protected function getTenantId(): ?string
    {
        // Try TenantContext first
        $tenantId = TenantContext::getTenantId();
        if ($tenantId) {
            return $tenantId;
        }

        // Fallback to TenantService
        $tenantService = service('tenant');
        return $tenantService->id();
    }

    /**
     * Override find method to apply tenant scope
     */
    public function find($id = null)
    {
        $this->applyTenantScope();
        return parent::find($id);
    }

    /**
     * Override where method to apply tenant scope
     */
    public function where($key = null, $value = null, bool $escape = null)
    {
        $this->applyTenantScope();
        return parent::where($key, $value, $escape);
    }

    /**
     * Override insert method to add tenant_id
     */
    public function insert($data = null, bool $returnID = true)
    {
        if ($this->tenantAware && is_array($data) && !isset($data[$this->tenantField])) {
            $tenantId = $this->getTenantId();
            if ($tenantId) {
                $data[$this->tenantField] = $tenantId;
            }
        }
        return parent::insert($data, $returnID);
    }

    /**
     * Override update method to add tenant_id
     */
    public function update($id = null, $data = null): bool
    {
        if ($this->tenantAware && is_array($data) && !isset($data[$this->tenantField])) {
            $tenantId = $this->getTenantId();
            if ($tenantId) {
                $data[$this->tenantField] = $tenantId;
            }
        }

        // Apply tenant scope for update
        $this->applyTenantScope();
        return parent::update($id, $data);
    }

    /**
     * Override delete method to apply tenant scope
     */
    public function delete($id = null, bool $purge = false)
    {
        $this->applyTenantScope();
        return parent::delete($id, $purge);
    }

    /**
     * Override countAllResults method to apply tenant scope
     */
    public function countAllResults(bool $reset = true, bool $test = false)
    {
        $this->applyTenantScope();
        return parent::countAllResults($reset, $test);
    }

    /**
     * Override paginate method to apply tenant scope
     */
    public function paginate(int $perPage = null, string $group = 'default', int $page = null, int $segment = 0)
    {
        $this->applyTenantScope();
        return parent::paginate($perPage, $group, $page, $segment);
    }

    /**
     * Reset tenant scope (useful for admin queries)
     */
    public function withoutTenantScope()
    {
        $this->tenantScopeApplied = false;
        return $this;
    }

    /**
     * Force apply tenant scope
     */
    public function withTenantScope()
    {
        $this->tenantScopeApplied = false;
        $this->applyTenantScope();
        return $this;
    }

    /**
     * Check if tenant scope is applied
     */
    public function isTenantScopeApplied(): bool
    {
        return $this->tenantScopeApplied;
    }

    /**
     * Get tenant-aware query builder
     */
    public function tenantAware()
    {
        $this->applyTenantScope();
        return $this;
    }

    /**
     * Get global query builder (ignores tenant scope)
     */
    public function global()
    {
        $this->tenantScopeApplied = true;
        return $this;
    }
}
