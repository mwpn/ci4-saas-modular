<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Swagger extends BaseConfig
{
    /**
     * Swagger/OpenAPI Configuration
     */

    public string $title = 'CI4 SaaS Modular API';
    public string $description = 'API Documentation for CodeIgniter 4 SaaS Modular Template';
    public string $version = '1.0.0';
    public string $termsOfService = 'https://example.com/terms';

    public array $contact = [
        'name' => 'API Support',
        'url' => 'https://example.com/contact',
        'email' => 'support@example.com'
    ];

    public array $license = [
        'name' => 'MIT',
        'url' => 'https://opensource.org/licenses/MIT'
    ];

    public array $servers = [
        [
            'url' => 'http://localhost:8080',
            'description' => 'Development server'
        ],
        [
            'url' => 'https://api.example.com',
            'description' => 'Production server'
        ]
    ];

    public array $securitySchemes = [
        'bearerAuth' => [
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT'
        ],
        'tenantHeader' => [
            'type' => 'apiKey',
            'in' => 'header',
            'name' => 'X-TENANT-ID'
        ]
    ];

    public array $tags = [
        [
            'name' => 'Authentication',
            'description' => 'Authentication and authorization endpoints'
        ],
        [
            'name' => 'Users',
            'description' => 'User management endpoints'
        ],
        [
            'name' => 'Tenants',
            'description' => 'Tenant management endpoints'
        ],
        [
            'name' => 'Dashboard',
            'description' => 'Dashboard and statistics endpoints'
        ]
    ];

    public array $schemas = [
        'User' => [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'format' => 'int64'],
                'tenant_id' => ['type' => 'integer', 'format' => 'int64'],
                'name' => ['type' => 'string'],
                'email' => ['type' => 'string', 'format' => 'email'],
                'role' => ['type' => 'string', 'enum' => ['super_admin', 'admin', 'user']],
                'status' => ['type' => 'string', 'enum' => ['active', 'inactive', 'pending']],
                'email_verified_at' => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                'last_login_at' => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                'created_at' => ['type' => 'string', 'format' => 'date-time'],
                'updated_at' => ['type' => 'string', 'format' => 'date-time']
            ]
        ],
        'Tenant' => [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'format' => 'int64'],
                'name' => ['type' => 'string'],
                'domain' => ['type' => 'string'],
                'status' => ['type' => 'string', 'enum' => ['active', 'inactive', 'suspended']],
                'settings' => ['type' => 'object'],
                'created_at' => ['type' => 'string', 'format' => 'date-time'],
                'updated_at' => ['type' => 'string', 'format' => 'date-time']
            ]
        ],
        'Error' => [
            'type' => 'object',
            'properties' => [
                'success' => ['type' => 'boolean'],
                'message' => ['type' => 'string'],
                'errors' => ['type' => 'object']
            ]
        ],
        'Success' => [
            'type' => 'object',
            'properties' => [
                'success' => ['type' => 'boolean'],
                'message' => ['type' => 'string'],
                'data' => ['type' => 'object']
            ]
        ]
    ];
}
