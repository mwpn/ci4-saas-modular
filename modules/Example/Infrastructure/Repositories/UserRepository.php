<?php

namespace Modules\Example\Infrastructure\Repositories;

use Modules\Example\Infrastructure\Models\UserModel;
use Modules\Example\Domain\Entities\User;

class UserRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    /**
     * Get all users
     */
    public function getAll(): array
    {
        return $this->model->findAll();
    }

    /**
     * Get user by ID
     */
    public function getById(string $id): ?User
    {
        return $this->model->find($id);
    }

    /**
     * Get user by email
     */
    public function getByEmail(string $email): ?User
    {
        return $this->model->getByEmail($email);
    }

    /**
     * Get active users
     */
    public function getActive(): array
    {
        return $this->model->getActive();
    }

    /**
     * Create new user
     */
    public function create(array $data): ?int
    {
        return $this->model->createUser($data);
    }

    /**
     * Update user
     */
    public function update(string $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    /**
     * Delete user
     */
    public function delete(string $id): bool
    {
        return $this->model->delete($id);
    }

    /**
     * Check if user is active
     */
    public function isActive(string $id): bool
    {
        return $this->model->isActive($id);
    }

    /**
     * Update user status
     */
    public function updateStatus(string $id, string $status): bool
    {
        return $this->model->updateStatus($id, $status);
    }

    /**
     * Update last login
     */
    public function updateLastLogin(string $id): bool
    {
        return $this->model->updateLastLogin($id);
    }

    /**
     * Mark email as verified
     */
    public function markEmailAsVerified(string $id): bool
    {
        return $this->model->markEmailAsVerified($id);
    }

    /**
     * Get user statistics
     */
    public function getStats(): array
    {
        return $this->model->getStats();
    }

    /**
     * Search users
     */
    public function search(string $query): array
    {
        return $this->model->searchUsers($query);
    }

    /**
     * Get users with pagination
     */
    public function getPaginated(int $perPage = 10, int $page = 1): array
    {
        return $this->model->getUsersPaginated($perPage, $page);
    }

    /**
     * Get recent users
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->model->getRecentUsers($limit);
    }

    /**
     * Get users by status
     */
    public function getByStatus(string $status): array
    {
        return $this->model->getUsersByStatus($status);
    }
}
