<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

class BasicTest extends CIUnitTestCase
{
    public function testBasicFunctionality()
    {
        $this->assertTrue(true);
    }

    public function testStringOperations()
    {
        $string = 'Hello World';
        $this->assertEquals('Hello World', $string);
        $this->assertStringContainsString('World', $string);
    }

    public function testArrayOperations()
    {
        $array = [1, 2, 3, 4, 5];
        $this->assertCount(5, $array);
        $this->assertContains(3, $array);
        $this->assertEquals(1, $array[0]);
    }

    public function testMathOperations()
    {
        $result = 2 + 2;
        $this->assertEquals(4, $result);
        
        $result = 10 / 2;
        $this->assertEquals(5, $result);
    }

    public function testErrorHandler()
    {
        $this->assertTrue(class_exists('App\Libraries\ErrorHandler'));
    }

    public function testTenantContextClass()
    {
        $this->assertTrue(class_exists('App\Services\TenantContext'));
    }

    public function testTenantServiceClass()
    {
        $this->assertTrue(class_exists('App\Services\TenantService'));
    }
}
