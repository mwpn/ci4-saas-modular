<?php
namespace Modules\Core\Presentation\Controllers;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        return 'Hello SaaS Modular!';
    }
}
