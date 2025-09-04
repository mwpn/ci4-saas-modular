<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class TenantFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        // Skip filter for certain routes
        $skipRoutes = [
            '/onboarding/choose-tenant',
            '/onboarding/set-tenant',
            '/onboarding/switch-tenant',
            '/login',
            '/register',
            '/api/health'
        ];

        foreach ($skipRoutes as $skipRoute) {
            if (strpos($path, $skipRoute) === 0) {
                return;
            }
        }

        $tenantService = service('tenant');

        // Check if tenant ID exists
        if (!$tenantService->id()) {
            return redirect()->to('/onboarding/choose-tenant');
        }

        // Check if tenant is valid and active
        if (!$tenantService->isValid()) {
            return redirect()->to('/onboarding/choose-tenant')->with('error', 'Tenant tidak valid atau tidak aktif');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
