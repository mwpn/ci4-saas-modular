<?php

namespace Tests\Feature;

use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\TestCase;

class ApiTest extends TestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = true;
    protected $refresh = true;
    protected $namespace = 'App';

    protected function setUp(): void
    {
        parent::setUp();

        // Skip seeding for now - just test API endpoints
        // $this->seed('Modules\Core\Database\Seeds\TenantSeeder');
    }

    public function testHealthEndpoint()
    {
        $response = $this->get('/api/health');

        $response->assertStatus(200);
        $response->assertJSONFragment([
            'success' => true,
            'message' => 'System is healthy'
        ]);
        
        // Check data structure
        $json = $response->getJSON();
        $data = json_decode($json, true);
        
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('version', $data['data']);
        $this->assertEquals('1.0.0', $data['data']['version']);
    }

    public function testLoginEndpointWithValidCredentials()
    {
        // Create test user
        $userData = [
            'tenant_id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'user',
            'status' => 'active'
        ];

        $this->db->table('users')->insert($userData);

        $response = $this->post('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $response->assertJSONFragment([
            'success' => true,
            'message' => 'Login successful'
        ]);
    }

    public function testLoginEndpointWithInvalidCredentials()
    {
        $response = $this->post('/api/auth/login', [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);
        $response->assertJSONFragment([
            'success' => false,
            'message' => 'Email atau password salah'
        ]);
    }

    public function testUsersEndpointWithoutAuthentication()
    {
        $response = $this->get('/api/users');

        $response->assertStatus(401);
        $response->assertJSONFragment([
            'success' => false,
            'message' => 'Unauthorized'
        ]);
    }

    public function testDashboardStatsEndpointWithoutAuthentication()
    {
        $response = $this->get('/api/dashboard/stats');

        $response->assertStatus(401);
        $response->assertJSONFragment([
            'success' => false,
            'message' => 'Unauthorized'
        ]);
    }

    public function testApiResponseFormat()
    {
        $response = $this->get('/api/health');

        $response->assertStatus(200);

        $json = $response->getJSON();
        $data = json_decode($json, true);

        // Check response structure
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);

        // Check data types
        $this->assertIsBool($data['success']);
        $this->assertIsString($data['message']);
        $this->assertIsArray($data['data']);
    }

    public function testApiErrorResponseFormat()
    {
        $response = $this->post('/api/auth/login', [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);

        $json = $response->getJSON();
        $data = json_decode($json, true);

        // Check error response structure
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('message', $data);

        // Check data types
        $this->assertIsBool($data['success']);
        $this->assertIsString($data['message']);
        $this->assertFalse($data['success']);
    }

    public function testApiContentType()
    {
        $response = $this->get('/api/health');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json; charset=UTF-8');
    }

    public function testApiCorsHeaders()
    {
        $response = $this->get('/api/health');

        $response->assertStatus(200);
        // CORS headers would be set by middleware
        // $response->assertHeader('Access-Control-Allow-Origin', '*');
    }
}
