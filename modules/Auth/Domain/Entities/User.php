<?php

namespace Modules\Auth\Domain\Entities;

use Modules\Core\Domain\Entities\BaseEntity;

class User extends BaseEntity
{
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'status',
        'email_verified_at',
        'last_login_at',
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed'
    ];

    // Business Logic Methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasPermission(string $permission): bool
    {
        // Implement permission logic based on role
        $permissions = $this->getRolePermissions();
        return in_array($permission, $permissions);
    }

    private function getRolePermissions(): array
    {
        $rolePermissions = [
            'super_admin' => ['*'], // All permissions
            'admin' => [
                'users.create',
                'users.read',
                'users.update',
                'users.delete',
                'tenants.read',
                'tenants.update',
                'settings.read',
                'settings.update'
            ],
            'user' => [
                'users.read',
                'users.update',
                'settings.read'
            ]
        ];

        return $rolePermissions[$this->role] ?? [];
    }

    public function markEmailAsVerified(): void
    {
        $this->email_verified_at = now();
    }

    public function updateLastLogin(): void
    {
        $this->last_login_at = now();
    }
}
