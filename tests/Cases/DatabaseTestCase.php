<?php

namespace Gbrock\Table\Tests\Cases;

use Illuminate\Filesystem\ClassFinder;
use Illuminate\Filesystem\Filesystem;

abstract class DatabaseTestCase extends TestCase
{
    /**
     * Setup DB before each test.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');

        $this->migrate();
    }

    /**
     * Run package database migrations.
     * Thanks http://stackoverflow.com/questions/27759301/setting-up-integration-tests-in-a-laravel-package
     *
     * @return void
     */
    public function migrate()
    {
        $fileSystem = new Filesystem;
        $classFinder = new ClassFinder;

        $packageMigrations = $fileSystem->files(__DIR__ . "/../../src/Migrations");
        $laravelMigrations = $fileSystem->files(__DIR__ . "/../../vendor/laravel/laravel/database/migrations");
        $testingMigrations = $fileSystem->files(__DIR__ . "/../migrations");

        $migrationFiles = array_merge($laravelMigrations, $packageMigrations);
        $migrationFiles = array_merge($migrationFiles, $testingMigrations);

        foreach ($migrationFiles as $file) {
            $fileSystem->requireOnce($file);
            $migrationClass = $classFinder->findClass($file);

            (new $migrationClass)->up();
        }
    }
}
