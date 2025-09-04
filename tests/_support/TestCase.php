<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Base test case for all tests
 */
class TestCase extends CIUnitTestCase
{
    /**
     * Set up test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Clean up test environment
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
