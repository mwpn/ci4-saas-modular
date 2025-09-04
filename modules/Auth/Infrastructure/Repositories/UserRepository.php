<?php

namespace Modules\Auth\Infrastructure\Repositories;

use Modules\Auth\Domain\Entities\User;
use Modules\Auth\Infrastructure\Models\UserModel;
use Modules\Core\Infrastructure\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    protected string $modelClass = UserModel::class;
    protected string $entityClass = User::class;

    public function findByEmail(string $email): ?User
    {
        $model = $this->model->findByEmail($email);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByEmailAndTenant(string $email, int $tenantId): ?User
    {
        $model = $this->model->findByEmailAndTenant($email, $tenantId);
        return $model ? $this->toEntity($model) : null;
    }

    public function getActiveUsers(): array
    {
        $models = $this->model->getActiveUsers();
        return array_map([$this, 'toEntity'], $models);
    }

    public function getUsersByRole(string $role): array
    {
        $models = $this->model->getUsersByRole($role);
        return array_map([$this, 'toEntity'], $models);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return $this->model->verifyPassword($password, $hash);
    }

    public function updateLastLogin(int $userId): bool
    {
        return $this->update($userId, [
            'last_login_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function markEmailAsVerified(int $userId): bool
    {
        return $this->update($userId, [
            'email_verified_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function changePassword(int $userId, string $newPassword): bool
    {
        return $this->update($userId, [
            'password' => $newPassword
        ]);
    }

    public function getUsersWithPagination(int $perPage = 10, int $page = 1): array
    {
        $result = $this->paginate($perPage, $page);

        return [
            'users' => $result['data'],
            'pager' => $result['pager']
        ];
    }

    public function searchUsers(string $query, int $perPage = 10, int $page = 1): array
    {
        $this->model->groupStart()
            ->like('name', $query)
            ->orLike('email', $query)
            ->groupEnd();

        $result = $this->paginate($perPage, $page);

        return [
            'users' => $result['data'],
            'pager' => $result['pager']
        ];
    }
}
