<?php

namespace Modules\Example\Presentation\Controllers;

use App\Controllers\BaseController;
use Modules\Example\Application\Services\UserApplicationService;

class UserController extends BaseController
{
    protected $userService;

    public function __construct()
    {
        $this->userService = new UserApplicationService();
    }

    /**
     * Display a listing of users
     */
    public function index()
    {
        $data = [
            'title' => 'Daftar Users',
            'users' => $this->userService->getAllUsers(),
            'stats' => $this->userService->getUserStats()
        ];

        return view('Modules\Example\Views\users\index', $data);
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah User Baru'
        ];

        return view('Modules\Example\Views\users\create', $data);
    }

    /**
     * Store a newly created user
     */
    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'phone' => 'permit_empty|min_length[10]|max_length[15]',
            'status' => 'required|in_list[active,inactive,suspended]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'phone' => $this->request->getPost('phone'),
                'status' => $this->request->getPost('status')
            ];

            $userId = $this->userService->createUser($data);

            if ($userId) {
                return redirect()->to('/users')->with('success', 'User berhasil dibuat');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal membuat user');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User tidak ditemukan');
        }

        $data = [
            'title' => 'Detail User: ' . $user->name,
            'user' => $user
        ];

        return view('Modules\Example\Views\users\show', $data);
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($id)
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User tidak ditemukan');
        }

        $data = [
            'title' => 'Edit User: ' . $user->name,
            'user' => $user
        ];

        return view('Modules\Example\Views\users\edit', $data);
    }

    /**
     * Update the specified user
     */
    public function update($id)
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'phone' => 'permit_empty|min_length[10]|max_length[15]',
            'status' => 'required|in_list[active,inactive,suspended]'
        ];

        // Password is optional for update
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'phone' => $this->request->getPost('phone'),
                'status' => $this->request->getPost('status')
            ];

            // Only update password if provided
            if ($this->request->getPost('password')) {
                $data['password'] = $this->request->getPost('password');
            }

            $success = $this->userService->updateUser($id, $data);

            if ($success) {
                return redirect()->to('/users')->with('success', 'User berhasil diperbarui');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui user');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified user
     */
    public function delete($id)
    {
        try {
            $success = $this->userService->deleteUser($id);

            if ($success) {
                return redirect()->to('/users')->with('success', 'User berhasil dihapus');
            } else {
                return redirect()->back()->with('error', 'Gagal menghapus user');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Activate user
     */
    public function activate($id)
    {
        try {
            $success = $this->userService->activateUser($id);

            if ($success) {
                return redirect()->back()->with('success', 'User berhasil diaktifkan');
            } else {
                return redirect()->back()->with('error', 'Gagal mengaktifkan user');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Deactivate user
     */
    public function deactivate($id)
    {
        try {
            $success = $this->userService->deactivateUser($id);

            if ($success) {
                return redirect()->back()->with('success', 'User berhasil dinonaktifkan');
            } else {
                return redirect()->back()->with('error', 'Gagal menonaktifkan user');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
