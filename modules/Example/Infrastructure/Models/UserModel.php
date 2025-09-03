<?php

namespace Modules\Example\Infrastructure\Models;

use CodeIgniter\Model;
use Modules\Example\Domain\Entities\User;
use Modules\Core\Infrastructure\Models\BaseModel;

class UserModel extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = User::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'status',
        'email_verified_at',
        'last_login_at',
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
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'phone' => 'permit_empty|min_length[10]|max_length[15]',
        'status' => 'required|in_list[active,inactive,suspended]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama user harus diisi',
            'min_length' => 'Nama user minimal 3 karakter',
            'max_length' => 'Nama user maksimal 100 karakter'
        ],
        'email' => [
            'required' => 'Email user harus diisi',
            'valid_email' => 'Format email tidak valid',
            'is_unique' => 'Email sudah digunakan'
        ],
        'password' => [
            'required' => 'Password harus diisi',
            'min_length' => 'Password minimal 6 karakter'
        ],
        'phone' => [
            'min_length' => 'Nomor telepon minimal 10 digit',
            'max_length' => 'Nomor telepon maksimal 15 digit'
        ],
        'status' => [
            'required' => 'Status user harus diisi',
            'in_list' => 'Status user tidak valid'
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
        // Remove password from update if empty
        if (isset($data['data']['password']) && empty($data['data']['password'])) {
            unset($data['data']['password']);
        }

        return $data;
    }

    /**
     * Get user by email
     */
    public function getByEmail(string $email): ?User
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Get active users only
     */
    public function getActive(): array
    {
        return $this->where('status', 'active')->findAll();
    }

    /**
     * Check if user is active
     */
    public function isActive(string $userId): bool
    {
        $user = $this->find($userId);
        return $user && $user->status === 'active';
    }

    /**
     * Create new user
     */
    public function createUser(array $data): ?int
    {
        $data['status'] = $data['status'] ?? 'active';

        if ($this->insert($data)) {
            return $this->getInsertID();
        }

        return null;
    }

    /**
     * Update user status
     */
    public function updateStatus(string $userId, string $status): bool
    {
        return $this->update($userId, ['status' => $status]);
    }

    /**
     * Update last login
     */
    public function updateLastLogin(string $userId): bool
    {
        return $this->update($userId, [
            'last_login_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Mark email as verified
     */
    public function markEmailAsVerified(string $userId): bool
    {
        return $this->update($userId, [
            'email_verified_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get user statistics
     */
    public function getStats(): array
    {
        return [
            'total' => $this->countAllResults(),
            'active' => $this->where('status', 'active')->countAllResults(),
            'inactive' => $this->where('status', 'inactive')->countAllResults(),
            'suspended' => $this->where('status', 'suspended')->countAllResults(),
            'verified' => $this->where('email_verified_at IS NOT NULL')->countAllResults(),
            'unverified' => $this->where('email_verified_at IS NULL')->countAllResults()
        ];
    }

    /**
     * Search users
     */
    public function searchUsers(string $query): array
    {
        return $this->groupStart()
            ->like('name', $query)
            ->orLike('email', $query)
            ->orLike('phone', $query)
            ->groupEnd()
            ->findAll();
    }

    /**
     * Get users with pagination
     */
    public function getUsersPaginated(int $perPage = 10, int $page = 1): array
    {
        return $this->paginate($perPage, 'default', $page);
    }

    /**
     * Get recent users
     */
    public function getRecentUsers(int $limit = 10): array
    {
        return $this->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get users by status
     */
    public function getUsersByStatus(string $status): array
    {
        return $this->where('status', $status)->findAll();
    }
}
