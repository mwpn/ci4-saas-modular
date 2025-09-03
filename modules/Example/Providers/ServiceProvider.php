<?php

namespace Modules\Example\Providers;

use CodeIgniter\Config\Services;
use Modules\Example\Application\Services\UserApplicationService;
use Modules\Example\Infrastructure\Repositories\UserRepository;

class ServiceProvider
{
    public function register(): void
    {
        // Bind User Repository
        Services::injectMock('userRepository', function () {
            return new UserRepository();
        });

        // Bind User Application Service
        Services::injectMock('userApplicationService', function () {
            return new UserApplicationService();
        });
    }

    public function boot(): void
    {
        // Boot logic here if needed
    }
}
