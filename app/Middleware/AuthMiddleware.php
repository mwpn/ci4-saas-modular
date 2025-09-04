<?php

namespace App\Middleware;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Modules\Auth\Application\Services\AuthService;
use Modules\Auth\Infrastructure\Repositories\UserRepository;

class AuthMiddleware implements FilterInterface
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

        // Check if user is active
        $user = $authService->getCurrentUser();
        if (!$user || !$user->isActive()) {
            $authService->logout();

            if ($request->isAJAX()) {
                return service('response')->setJSON([
                    'success' => false,
                    'message' => 'Account is inactive',
                    'redirect' => base_url('login')
                ])->setStatusCode(403);
            }

            return redirect()->to('login')->with('message', 'Your account is inactive');
        }

        // Set user data in request for easy access
        $request->user = $user;

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
