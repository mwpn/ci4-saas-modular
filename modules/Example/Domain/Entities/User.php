<?php

namespace Modules\Example\Domain\Entities;

use Modules\Core\Domain\Entities\BaseEntity;

class User extends BaseEntity
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
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
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if email is verified
     */
    public function isEmailVerified(): bool
    {
        return !empty($this->email_verified_at);
    }

    /**
     * Get user's full name
     */
    public function getFullName(): string
    {
        return $this->name;
    }

    /**
     * Get user's initials
     */
    public function getInitials(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        return substr($initials, 0, 2);
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrl(): string
    {
        if (!empty($this->avatar)) {
            return base_url('uploads/avatars/' . $this->avatar);
        }

        // Generate default avatar with initials
        $initials = $this->getInitials();
        return "https://ui-avatars.com/api/?name={$initials}&background=random&color=fff&size=128";
    }

    /**
     * Get last login formatted
     */
    public function getLastLoginFormatted(): string
    {
        if (empty($this->last_login_at)) {
            return 'Belum pernah login';
        }

        return date('d/m/Y H:i', strtotime($this->last_login_at));
    }

    /**
     * Update last login
     */
    public function updateLastLogin(): void
    {
        $this->last_login_at = date('Y-m-d H:i:s');
    }

    /**
     * Mark email as verified
     */
    public function markEmailAsVerified(): void
    {
        $this->email_verified_at = date('Y-m-d H:i:s');
    }
}
