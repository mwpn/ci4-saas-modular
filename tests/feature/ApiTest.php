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

    public function testUsersEndpointWithoutAuthentication()
    {
        $response = $this->get('/api/users');

        // Should redirect to login or return 401
        $statusCode = $response->getStatusCode();
        $this->assertTrue(in_array($statusCode, [302, 401, 403]), "Expected 302, 401, or 403, got {$statusCode}");
    }

    public function testDashboardStatsEndpointWithoutAuthentication()
    {
        $response = $this->get('/api/dashboard/stats');

        // Should redirect to login or return 401
        $statusCode = $response->getStatusCode();
        $this->assertTrue(in_array($statusCode, [302, 401, 403]), "Expected 302, 401, or 403, got {$statusCode}");
    }

    public function testApiResponseFormat()
    {
        $response = $this->get('/api/health');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json; charset=UTF-8');
    }

    public function testApiErrorResponseFormat()
    {
        $response = $this->get('/api/nonexistent');

        // Should return 404 or redirect
        $this->assertTrue(in_array($response->getStatusCode(), [404, 302]));
    }

    public function testSwaggerDocumentationAccess()
    {
        $response = $this->get('/api/docs');

        $response->assertStatus(302); // Should redirect to swagger-ui
    }
}