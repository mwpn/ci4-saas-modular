<?php

namespace Modules\Auth\Presentation\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use Modules\Auth\Application\Services\AuthService;
use Modules\Core\Presentation\Controllers\BaseController;

class AuthController extends BaseController
{
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService(
            new \Modules\Auth\Infrastructure\Repositories\UserRepository()
        );
    }

    public function login(): string
    {
        if ($this->authService->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }

        return view('Modules\Auth\Presentation\Views\auth\login');
    }

    public function attemptLogin(): ResponseInterface
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[8]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember') === '1';

        $result = $this->authService->login($email, $password, $remember);

        if ($result['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $result['message'],
                'redirect' => '/dashboard'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $result['message']
        ]);
    }

    public function register(): string
    {
        if ($this->authService->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }

        return view('Modules\Auth\Presentation\Views\auth\register');
    }

    public function attemptRegister(): ResponseInterface
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $userData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => 'user',
            'status' => 'active'
        ];

        $result = $this->authService->register($userData);

        if ($result['success']) {
            // Auto login after registration
            $this->authService->login($userData['email'], $userData['password']);

            return $this->response->setJSON([
                'success' => true,
                'message' => $result['message'],
                'redirect' => '/dashboard'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $result['message']
        ]);
    }

    public function logout(): ResponseInterface
    {
        $this->authService->logout();

        return redirect()->to('/login')->with('message', 'Logout berhasil');
    }

    public function forgotPassword(): string
    {
        return view('Modules\Auth\Presentation\Views\auth\forgot_password');
    }

    public function attemptForgotPassword(): ResponseInterface
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $email = $this->request->getPost('email');
        $result = $this->authService->resetPassword($email);

        return $this->response->setJSON($result);
    }

    public function resetPassword(string $token): string
    {
        return view('Modules\Auth\Presentation\Views\auth\reset_password', [
            'token' => $token
        ]);
    }

    public function attemptResetPassword(): ResponseInterface
    {
        $rules = [
            'token' => 'required',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        // TODO: Implement token validation and password reset

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Password berhasil direset',
            'redirect' => '/login'
        ]);
    }

    public function profile(): string
    {
        $user = $this->authService->getCurrentUser();

        if (!$user) {
            return redirect()->to('/login');
        }

        return view('Modules\Auth\Presentation\Views\auth\profile', [
            'user' => $user
        ]);
    }

    public function updateProfile(): ResponseInterface
    {
        $user = $this->authService->getCurrentUser();

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $userData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email')
        ];

        try {
            $userRepository = new \Modules\Auth\Infrastructure\Repositories\UserRepository();
            $userRepository->update($user->id, $userData);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Profile berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate profile: ' . $e->getMessage()
            ]);
        }
    }

    public function changePassword(): ResponseInterface
    {
        $user = $this->authService->getCurrentUser();

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'new_password_confirm' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        $result = $this->authService->changePassword(
            $user->id,
            $currentPassword,
            $newPassword
        );

        return $this->response->setJSON($result);
    }
}
