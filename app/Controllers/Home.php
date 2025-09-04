<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Home extends Controller
{
    public function index(): string
    {
        $data = [
            'title' => 'CI4 SaaS Modular Template',
            'description' => 'Template CodeIgniter 4 untuk aplikasi SaaS multi-tenant yang powerful dan scalable',
            'features' => [
                'Multi-Tenant Architecture',
                'Modular Monolith Design',
                'Authentication & Authorization',
                'RESTful API dengan Swagger',
                'Docker Support',
                'Testing Framework',
                'Clean Architecture'
            ],
            'stats' => [
                'tenants' => 0,
                'users' => 0,
                'modules' => 5
            ]
        ];

        return view('landing/index', $data);
    }
}
