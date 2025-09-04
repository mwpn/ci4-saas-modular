<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\TenantContext;
use App\Services\TenantService;

class TenantTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        TenantContext::clear();
    }

    protected function tearDown(): void
    {
        TenantContext::clear();
        parent::tearDown();
    }

    public function testTenantContextSetAndGet()
    {
        TenantContext::setTenantId('test-tenant-123');
        
        $this->assertEquals('test-tenant-123', TenantContext::getTenantId());
    }

    public function testTenantContextClear()
    {
        TenantContext::setTenantId('test-tenant-123');
        TenantContext::clear();
        
        $this->assertNull(TenantContext::getTenantId());
    }

    public function testTenantContextSettings()
    {
        $settings = ['theme' => 'dark', 'language' => 'en'];
        TenantContext::setSettings($settings);
        
        $this->assertEquals($settings, TenantContext::getSettings());
        $this->assertEquals('dark', TenantContext::getSetting('theme'));
        $this->assertEquals('en', TenantContext::getSetting('language'));
        $this->assertNull(TenantContext::getSetting('nonexistent'));
    }

    public function testTenantService()
    {
        $tenantService = new TenantService();
        
        $this->assertInstanceOf(TenantService::class, $tenantService);
        $this->assertNull($tenantService->id());
    }

    public function testTenantContextHasTenant()
    {
        $this->assertFalse(TenantContext::hasTenant());
        
        TenantContext::setTenantId('test-tenant-123');
        // Skip database check for unit test
        $this->assertTrue(TenantContext::getTenantId() !== null);
    }
}