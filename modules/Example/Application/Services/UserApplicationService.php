<?php

namespace Modules\Example\Application\Services;

use Modules\Example\Infrastructure\Repositories\UserRepository;
use Modules\Example\Domain\Entities\User;

class UserApplicationService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    /**
     * Get all users
     */
    public function getAllUsers(): array
    {
        return $this->repository->getAll();
    }

    /**
     * Get user by ID
     */
    public function getUserById(string $id): ?User
    {
        return $this->repository->getById($id);
    }

    /**
     * Get user by email
     */
    public function getUserByEmail(string $email): ?User
    {
        return $this->repository->getByEmail($email);
    }

    /**
     * Create new user
     */
    public function createUser(array $data): ?int
    {
        // Validate required fields
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('Nama user harus diisi');
        }

        if (empty($data['email'])) {
            throw new \InvalidArgumentException('Email user harus diisi');
        }

        // Check if email already exists
        if ($this->repository->getByEmail($data['email'])) {
            throw new \InvalidArgumentException('Email sudah digunakan');
        }

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->repository->create($data);
    }

    /**
     * Update user
     */
    public function updateUser(string $id, array $data): bool
    {
        $user = $this->repository->getById($id);
        if (!$user) {
            throw new \InvalidArgumentException('User tidak ditemukan');
        }

        // Check if email already exists (excluding current user)
        if (isset($data['email']) && $data['email'] !== $user->email) {
            $existingUser = $this->repository->getByEmail($data['email']);
            if ($existingUser && $existingUser->id !== $id) {
                throw new \InvalidArgumentException('Email sudah digunakan');
            }
        }

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->repository->update($id, $data);
    }

    /**
     * Delete user
     */
    public function deleteUser(string $id): bool
    {
        $user = $this->repository->getById($id);
        if (!$user) {
            throw new \InvalidArgumentException('User tidak ditemukan');
        }

        return $this->repository->delete($id);
    }

    /**
     * Activate user
     */
    public function activateUser(string $id): bool
    {
        return $this->repository->updateStatus($id, 'active');
    }

    /**
     * Deactivate user
     */
    public function deactivateUser(string $id): bool
    {
        return $this->repository->updateStatus($id, 'inactive');
    }

    /**
     * Get user statistics
     */
    public function getUserStats(): array
    {
        return $this->repository->getStats();
    }

    /**
     * Search users
     */
    public function searchUsers(string $query): array
    {
        return $this->repository->search($query);
    }

    /**
     * Get users with pagination
     */
    public function getUsersPaginated(int $perPage = 10, int $page = 1): array
    {
        return $this->repository->getPaginated($perPage, $page);
    }

    /**
     * Validate user credentials
     */
    public function validateCredentials(string $email, string $password): ?User
    {
        $user = $this->repository->getByEmail($email);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }
}
