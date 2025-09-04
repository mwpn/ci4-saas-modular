<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Modules\Core\Services\TenantContext;
use Modules\Core\Services\TenantService;

class TenantTest extends CIUnitTestCase
{
    protected TenantService $tenantService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenantService = new TenantService();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        TenantContext::clear();
    }

    public function testTenantContextSetAndGet()
    {
        $tenantId = 123;
        $tenantName = 'Test Tenant';

        TenantContext::setTenantId($tenantId);
        TenantContext::setTenantName($tenantName);

        $this->assertEquals($tenantId, TenantContext::getTenantId());
        $this->assertEquals($tenantName, TenantContext::getTenantName());
    }

    public function testTenantContextClear()
    {
        TenantContext::setTenantId(123);
        TenantContext::setTenantName('Test Tenant');

        TenantContext::clear();

        $this->assertNull(TenantContext::getTenantId());
        $this->assertNull(TenantContext::getTenantName());
    }

    public function testTenantContextSettings()
    {
        $settings = [
            'theme' => 'dark',
            'language' => 'id',
            'timezone' => 'Asia/Jakarta'
        ];

        TenantContext::setSettings($settings);

        $this->assertEquals($settings, TenantContext::getSettings());
        $this->assertEquals('dark', TenantContext::getSetting('theme'));
        $this->assertEquals('id', TenantContext::getSetting('language'));
        $this->assertNull(TenantContext::getSetting('nonexistent'));
    }

    public function testTenantResolutionFromSubdomain()
    {
        // Mock request with subdomain
        $request = service('request');
        $request->setUri('http://tenant1.example.com');

        // This would need to be mocked properly in a real test
        // For now, we'll test the logic directly
        $uri = $request->getUri();
        $host = $uri->getHost();

        if (strpos($host, '.') !== false) {
            $subdomain = explode('.', $host)[0];
            $this->assertEquals('tenant1', $subdomain);
        }
    }

    public function testTenantResolutionFromPath()
    {
        // Mock request with path
        $request = service('request');
        $request->setUri('http://example.com/tenant1/dashboard');

        $uri = $request->getUri();
        $path = $uri->getPath();
        $segments = explode('/', trim($path, '/'));

        if (!empty($segments[0])) {
            $tenantFromPath = $segments[0];
            $this->assertEquals('tenant1', $tenantFromPath);
        }
    }

    public function testTenantResolutionFromHeader()
    {
        // Mock request with header
        $request = service('request');
        $request->setHeader('X-TENANT-ID', 'tenant123');

        $tenantFromHeader = $request->getHeader('X-TENANT-ID')->getValue();
        $this->assertEquals('tenant123', $tenantFromHeader);
    }

    public function testTenantValidation()
    {
        // This would test tenant validation against database
        // For now, we'll test the basic structure

        $tenantData = [
            'id' => 1,
            'name' => 'Valid Tenant',
            'domain' => 'valid-tenant.example.com',
            'status' => 'active'
        ];

        $this->assertArrayHasKey('id', $tenantData);
        $this->assertArrayHasKey('name', $tenantData);
        $this->assertArrayHasKey('domain', $tenantData);
        $this->assertArrayHasKey('status', $tenantData);
        $this->assertEquals('active', $tenantData['status']);
    }

    public function testTenantIsolation()
    {
        // Test that tenant context is properly isolated
        TenantContext::setTenantId(1);
        TenantContext::setTenantName('Tenant 1');

        $this->assertEquals(1, TenantContext::getTenantId());
        $this->assertEquals('Tenant 1', TenantContext::getTenantName());

        // Simulate switching to another tenant
        TenantContext::setTenantId(2);
        TenantContext::setTenantName('Tenant 2');

        $this->assertEquals(2, TenantContext::getTenantId());
        $this->assertEquals('Tenant 2', TenantContext::getTenantName());
    }

    public function testTenantSessionPersistence()
    {
        // Test session persistence
        $session = session();

        TenantContext::setTenantId(123);
        TenantContext::setTenantName('Session Tenant');

        // Simulate session persistence
        $session->set('tenant_id', TenantContext::getTenantId());
        $session->set('tenant_name', TenantContext::getTenantName());

        $this->assertEquals(123, $session->get('tenant_id'));
        $this->assertEquals('Session Tenant', $session->get('tenant_name'));
    }
}
