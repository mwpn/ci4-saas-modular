<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Modules\Auth\Application\Services\AuthService;
use Modules\Auth\Infrastructure\Repositories\UserRepository;
use Modules\Core\Services\TenantContext;

class AuthTest extends CIUnitTestCase
{
    protected AuthService $authService;
    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = new UserRepository();
        $this->authService = new AuthService($this->userRepository);

        // Mock tenant context
        TenantContext::setTenantId(1);
        TenantContext::setTenantName('Test Tenant');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        TenantContext::clear();
    }

    public function testLoginWithValidCredentials()
    {
        // Create test user
        $userData = [
            'tenant_id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'user',
            'status' => 'active'
        ];

        $user = $this->userRepository->create($userData);

        // Test login
        $result = $this->authService->login('test@example.com', 'password123');

        $this->assertTrue($result['success']);
        $this->assertEquals('Login berhasil', $result['message']);
        $this->assertInstanceOf('Modules\Auth\Domain\Entities\User', $result['user']);

        // Cleanup
        $this->userRepository->delete($user->id);
    }

    public function testLoginWithInvalidCredentials()
    {
        $result = $this->authService->login('invalid@example.com', 'wrongpassword');

        $this->assertFalse($result['success']);
        $this->assertEquals('Email atau password salah', $result['message']);
    }

    public function testLoginWithInactiveUser()
    {
        // Create inactive user
        $userData = [
            'tenant_id' => 1,
            'name' => 'Inactive User',
            'email' => 'inactive@example.com',
            'password' => 'password123',
            'role' => 'user',
            'status' => 'inactive'
        ];

        $user = $this->userRepository->create($userData);

        // Test login
        $result = $this->authService->login('inactive@example.com', 'password123');

        $this->assertFalse($result['success']);
        $this->assertEquals('Akun tidak aktif', $result['message']);

        // Cleanup
        $this->userRepository->delete($user->id);
    }

    public function testRegisterNewUser()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'role' => 'user',
            'status' => 'active'
        ];

        $result = $this->authService->register($userData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Registrasi berhasil', $result['message']);
        $this->assertInstanceOf('Modules\Auth\Domain\Entities\User', $result['user']);

        // Cleanup
        $this->userRepository->delete($result['user']->id);
    }

    public function testRegisterWithExistingEmail()
    {
        // Create existing user
        $userData = [
            'tenant_id' => 1,
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'role' => 'user',
            'status' => 'active'
        ];

        $user = $this->userRepository->create($userData);

        // Try to register with same email
        $newUserData = [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'role' => 'user',
            'status' => 'active'
        ];

        $result = $this->authService->register($newUserData);

        $this->assertFalse($result['success']);
        $this->assertEquals('Email sudah digunakan', $result['message']);

        // Cleanup
        $this->userRepository->delete($user->id);
    }

    public function testUserPermissions()
    {
        // Create admin user
        $userData = [
            'tenant_id' => 1,
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'role' => 'admin',
            'status' => 'active'
        ];

        $user = $this->userRepository->create($userData);

        // Test permissions
        $this->assertTrue($user->hasPermission('users.create'));
        $this->assertTrue($user->hasPermission('users.read'));
        $this->assertTrue($user->hasPermission('users.update'));
        $this->assertTrue($user->hasPermission('users.delete'));
        $this->assertFalse($user->hasPermission('super.admin'));

        // Cleanup
        $this->userRepository->delete($user->id);
    }

    public function testSuperAdminPermissions()
    {
        // Create super admin user
        $userData = [
            'tenant_id' => 1,
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => 'password123',
            'role' => 'super_admin',
            'status' => 'active'
        ];

        $user = $this->userRepository->create($userData);

        // Test permissions
        $this->assertTrue($user->hasPermission('*'));
        $this->assertTrue($user->hasPermission('any.permission'));

        // Cleanup
        $this->userRepository->delete($user->id);
    }

    public function testChangePassword()
    {
        // Create user
        $userData = [
            'tenant_id' => 1,
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'oldpassword',
            'role' => 'user',
            'status' => 'active'
        ];

        $user = $this->userRepository->create($userData);

        // Test password change
        $result = $this->authService->changePassword($user->id, 'oldpassword', 'newpassword');

        $this->assertTrue($result['success']);
        $this->assertEquals('Password berhasil diubah', $result['message']);

        // Test login with new password
        $loginResult = $this->authService->login('testuser@example.com', 'newpassword');
        $this->assertTrue($loginResult['success']);

        // Cleanup
        $this->userRepository->delete($user->id);
    }

    public function testChangePasswordWithWrongCurrentPassword()
    {
        // Create user
        $userData = [
            'tenant_id' => 1,
            'name' => 'Test User',
            'email' => 'testuser2@example.com',
            'password' => 'correctpassword',
            'role' => 'user',
            'status' => 'active'
        ];

        $user = $this->userRepository->create($userData);

        // Test password change with wrong current password
        $result = $this->authService->changePassword($user->id, 'wrongpassword', 'newpassword');

        $this->assertFalse($result['success']);
        $this->assertEquals('Password lama salah', $result['message']);

        // Cleanup
        $this->userRepository->delete($user->id);
    }
}
