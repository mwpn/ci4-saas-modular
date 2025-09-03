<?php

namespace Modules\Core\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Demo Tenant',
                'slug' => 'demo',
                'domain' => 'demo.localhost',
                'database_name' => 'tenant_demo',
                'status' => 'active',
                'settings' => json_encode([
                    'theme' => 'default',
                    'timezone' => 'Asia/Jakarta',
                    'currency' => 'IDR',
                    'language' => 'id'
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Test Tenant',
                'slug' => 'test',
                'domain' => 'test.localhost',
                'database_name' => 'tenant_test',
                'status' => 'active',
                'settings' => json_encode([
                    'theme' => 'dark',
                    'timezone' => 'Asia/Jakarta',
                    'currency' => 'USD',
                    'language' => 'en'
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $this->db->table('tenants')->insertBatch($data);
    }
}
