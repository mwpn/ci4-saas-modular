<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class TenantFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! service('tenant')->id()) {
            return redirect()->to('/onboarding/choose-tenant');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
