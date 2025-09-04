<?php

namespace Modules\Auth\Providers;

use CodeIgniter\Config\BaseService;
use Modules\Auth\Application\Services\AuthService;
use Modules\Auth\Infrastructure\Repositories\UserRepository;

class ServiceProvider extends BaseService
{
    public function register(): void
    {
        // Register Auth Service
        service('auth', function ($getShared = true) {
            if ($getShared) {
                return static::getSharedInstance('auth');
            }
            return new AuthService(new UserRepository());
        });

        // Register User Repository
        service('userRepository', function ($getShared = true) {
            if ($getShared) {
                return static::getSharedInstance('userRepository');
            }
            return new UserRepository();
        });
    }

    public function boot(): void
    {
        // Boot logic if needed
    }
}
