<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use Modules\Auth\Application\Services\AuthService;
use Modules\Auth\Infrastructure\Repositories\UserRepository;

class RegisterController extends BaseController
{
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService(
            new UserRepository()
        );
    }

    public function index(): string
    {
        if ($this->authService->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }

        return view('register');
    }

    public function attemptRegister(): ResponseInterface
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Validasi gagal');
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

            return redirect()->to('/dashboard')
                ->with('success', 'Registrasi berhasil! Selamat datang!');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $result['message']);
    }
}
