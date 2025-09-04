<?php

namespace Modules\Auth\Application\Services;

use Modules\Auth\Domain\Entities\User;
use Modules\Auth\Infrastructure\Repositories\UserRepository;
use App\Services\TenantContext;
use CodeIgniter\HTTP\ResponseInterface;

class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(string $email, string $password, bool $remember = false): array
    {
        $tenantId = TenantContext::getTenantId();

        if (!$tenantId) {
            return [
                'success' => false,
                'message' => 'Tenant tidak ditemukan'
            ];
        }

        $user = $this->userRepository->findByEmailAndTenant($email, $tenantId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email atau password salah'
            ];
        }

        if (!$user->isActive()) {
            return [
                'success' => false,
                'message' => 'Akun tidak aktif'
            ];
        }

        if (!$this->userRepository->verifyPassword($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Email atau password salah'
            ];
        }

        // Update last login
        $this->userRepository->updateLastLogin($user->id);

        // Set session
        $this->setUserSession($user, $remember);

        return [
            'success' => true,
            'message' => 'Login berhasil',
            'user' => $user
        ];
    }

    public function logout(): void
    {
        $session = session();
        $session->destroy();
    }

    public function register(array $userData): array
    {
        $tenantId = TenantContext::getTenantId();

        if (!$tenantId) {
            return [
                'success' => false,
                'message' => 'Tenant tidak ditemukan'
            ];
        }

        // Check if email already exists
        $existingUser = $this->userRepository->findByEmailAndTenant($userData['email'], $tenantId);
        if ($existingUser) {
            return [
                'success' => false,
                'message' => 'Email sudah digunakan'
            ];
        }

        // Set default values
        $userData['tenant_id'] = $tenantId;
        $userData['role'] = $userData['role'] ?? 'user';
        $userData['status'] = $userData['status'] ?? 'pending';

        try {
            $user = $this->userRepository->create($userData);

            return [
                'success' => true,
                'message' => 'Registrasi berhasil',
                'user' => $user
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Registrasi gagal: ' . $e->getMessage()
            ];
        }
    }

    public function getCurrentUser(): ?User
    {
        $session = session();
        $userId = $session->get('user_id');

        if (!$userId) {
            return null;
        }

        return $this->userRepository->find($userId);
    }

    public function isLoggedIn(): bool
    {
        return $this->getCurrentUser() !== null;
    }

    public function hasPermission(string $permission): bool
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            return false;
        }

        return $user->hasPermission($permission);
    }

    public function hasRole(string $role): bool
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            return false;
        }

        return $user->hasRole($role);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('super_admin');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): array
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User tidak ditemukan'
            ];
        }

        if (!$this->userRepository->verifyPassword($currentPassword, $user->password)) {
            return [
                'success' => false,
                'message' => 'Password lama salah'
            ];
        }

        try {
            $this->userRepository->changePassword($userId, $newPassword);

            return [
                'success' => true,
                'message' => 'Password berhasil diubah'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengubah password: ' . $e->getMessage()
            ];
        }
    }

    public function resetPassword(string $email): array
    {
        $tenantId = TenantContext::getTenantId();
        $user = $this->userRepository->findByEmailAndTenant($email, $tenantId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email tidak ditemukan'
            ];
        }

        // Generate reset token
        $resetToken = bin2hex(random_bytes(32));

        // Store reset token in session or database
        $session = session();
        $session->set('reset_token_' . $user->id, $resetToken);
        $session->set('reset_token_expiry_' . $user->id, time() + 3600); // 1 hour

        // TODO: Send email with reset link

        return [
            'success' => true,
            'message' => 'Link reset password telah dikirim ke email Anda',
            'reset_token' => $resetToken // For testing, remove in production
        ];
    }

    private function setUserSession(User $user, bool $remember = false): void
    {
        $session = session();

        $session->set([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'tenant_id' => $user->tenant_id,
            'is_logged_in' => true
        ]);

        if ($remember) {
            // Set remember me cookie
            $rememberToken = bin2hex(random_bytes(32));
            $session->set('remember_token', $rememberToken);

            // TODO: Store remember token in database
        }
    }
}
