<?php

namespace Modules\Auth\Infrastructure\Models;

use Modules\Core\Infrastructure\Models\BaseModel;

class UserModel extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'Modules\Auth\Domain\Entities\User';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
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

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]',
        'role' => 'required|in_list[super_admin,admin,user]',
        'status' => 'required|in_list[active,inactive,pending]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama harus diisi',
            'min_length' => 'Nama minimal 3 karakter',
            'max_length' => 'Nama maksimal 100 karakter'
        ],
        'email' => [
            'required' => 'Email harus diisi',
            'valid_email' => 'Format email tidak valid',
            'is_unique' => 'Email sudah digunakan'
        ],
        'password' => [
            'required' => 'Password harus diisi',
            'min_length' => 'Password minimal 8 karakter'
        ],
        'role' => [
            'required' => 'Role harus diisi',
            'in_list' => 'Role tidak valid'
        ],
        'status' => [
            'required' => 'Status harus diisi',
            'in_list' => 'Status tidak valid'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    // Custom Methods
    public function findByEmail(string $email): ?object
    {
        return $this->where('email', $email)->first();
    }

    public function findByEmailAndTenant(string $email, int $tenantId): ?object
    {
        return $this->where('email', $email)
            ->where('tenant_id', $tenantId)
            ->first();
    }

    public function getActiveUsers(): array
    {
        return $this->where('status', 'active')->findAll();
    }

    public function getUsersByRole(string $role): array
    {
        return $this->where('role', $role)->findAll();
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
