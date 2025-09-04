<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Modules\Auth\Application\Services\AuthService;
use Modules\Auth\Infrastructure\Repositories\UserRepository;
use App\Services\TenantContext;

class DashboardController extends Controller
{
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService(new UserRepository());
    }

    public function index(): string
    {
        // Check authentication
        if (!$this->authService->isLoggedIn()) {
            return redirect()->to('login');
        }

        // Get dashboard statistics
        $stats = $this->getDashboardStats();

        // Get recent activities (mock data for now)
        $recentActivities = $this->getRecentActivities();

        $data = [
            'title' => 'Dashboard',
            'pageTitle' => 'Dashboard',
            'stats' => $stats,
            'recent_activities' => $recentActivities
        ];

        return view('dashboard/index', $data);
    }

    private function getDashboardStats(): array
    {
        $userRepository = new UserRepository();
        $tenantId = TenantContext::getTenantId();

        try {
            // Get total users for current tenant
            $totalUsers = $userRepository->model->where('tenant_id', $tenantId)->countAllResults();

            // Get active users
            $activeUsers = $userRepository->model->where('tenant_id', $tenantId)
                ->where('status', 'active')
                ->countAllResults();

            // Mock data for other stats
            $totalTenants = 1; // Current tenant
            $revenue = 0; // Mock revenue

            return [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'total_tenants' => $totalTenants,
                'revenue' => $revenue
            ];
        } catch (\Exception $e) {
            // Return default stats if database error
            return [
                'total_users' => 0,
                'active_users' => 0,
                'total_tenants' => 1,
                'revenue' => 0
            ];
        }
    }

    private function getRecentActivities(): array
    {
        // Mock recent activities data
        // In a real application, this would come from an activity log table
        return [
            [
                'user_name' => 'John Doe',
                'action' => 'Created new user',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'status' => 'success'
            ],
            [
                'user_name' => 'Jane Smith',
                'action' => 'Updated tenant settings',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'status' => 'success'
            ],
            [
                'user_name' => 'Admin User',
                'action' => 'System backup completed',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours')),
                'status' => 'success'
            ],
            [
                'user_name' => 'Test User',
                'action' => 'Failed login attempt',
                'created_at' => date('Y-m-d H:i:s', strtotime('-4 hours')),
                'status' => 'warning'
            ]
        ];
    }
}
