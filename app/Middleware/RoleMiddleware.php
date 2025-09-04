<?php

namespace App\Middleware;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Modules\Auth\Application\Services\AuthService;
use Modules\Auth\Infrastructure\Repositories\UserRepository;

class RoleMiddleware implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authService = new AuthService(new UserRepository());

        if (!$authService->isLoggedIn()) {
            if ($request->isAJAX()) {
                return service('response')->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'redirect' => base_url('login')
                ])->setStatusCode(401);
            }

            return redirect()->to('login')->with('message', 'Please login first');
        }

        // Check required roles
        if (!empty($arguments)) {
            $user = $authService->getCurrentUser();
            $hasRequiredRole = false;

            foreach ($arguments as $role) {
                if ($user->hasRole($role)) {
                    $hasRequiredRole = true;
                    break;
                }
            }

            if (!$hasRequiredRole) {
                if ($request->isAJAX()) {
                    return service('response')->setJSON([
                        'success' => false,
                        'message' => 'Insufficient permissions',
                        'redirect' => base_url('dashboard')
                    ])->setStatusCode(403);
                }

                return redirect()->to('dashboard')->with('error', 'You do not have permission to access this page');
            }
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
