<?php

namespace Tests\Feature;

use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\TestCase;

class HealthTest extends TestCase
{
    use FeatureTestTrait;

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

    public function testApiResponseFormat()
    {
        $response = $this->get('/api/health');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json; charset=UTF-8');
    }

    public function testSwaggerDocumentationAccess()
    {
        $response = $this->get('/api/docs');

        $response->assertStatus(302); // Should redirect to swagger-ui
    }
}
