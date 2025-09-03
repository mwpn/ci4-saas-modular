<?php

namespace Config;

use CodeIgniter\Events\Events as CIEvents;

class Events
{
    public function __construct()
    {
        $this->registerEvents();
    }

    private function registerEvents(): void
    {
        // Autoload ServiceProvider setiap module
        CIEvents::on('pre_system', static function () {
            $modulesPath = ROOTPATH . 'modules';
            foreach (glob($modulesPath . '/*/Providers/ServiceProvider.php') as $provider) {
                require_once $provider;
                $ns = 'Modules\\' . basename(dirname(dirname($provider))) . '\\Providers\\ServiceProvider';
                if (class_exists($ns)) {
                    (new $ns())->register();
                }
            }
        });

        // Autoload Routes setiap module
        CIEvents::on('pre_controller', static function () {
            $modulesPath = ROOTPATH . 'modules';
            $routes = service('routes');
            foreach (glob($modulesPath . '/*/Routes.php') as $routeFile) {
                require $routeFile;
            }
        });
    }
}
