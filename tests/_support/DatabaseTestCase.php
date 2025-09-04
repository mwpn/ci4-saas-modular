<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

abstract class DatabaseTestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = true;
    protected $refresh = true;
    protected $namespace = 'App';

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test database
        $this->setUpDatabase();
    }

    protected function tearDown(): void
    {
        $this->tearDownDatabase();
        parent::tearDown();
    }

    protected function setUpDatabase(): void
    {
        // Run migrations for test database
        if ($this->migrate) {
            $this->runMigrations();
        }
    }

    protected function tearDownDatabase(): void
    {
        // Clean up test database
        if ($this->refresh) {
            $this->refreshDatabase();
        }
    }

    protected function runMigrations(): void
    {
        // Run all migrations
        $migrate = \Config\Database::migrations();
        if ($migrate) {
            $migrate->setNamespace($this->namespace);
            $migrate->latest();
        }
    }

    protected function refreshDatabase(): void
    {
        // Refresh database for clean state
        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        
        // Get all tables
        $tables = $db->listTables();
        foreach ($tables as $table) {
            $db->query("TRUNCATE TABLE `{$table}`");
        }
        
        $db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
