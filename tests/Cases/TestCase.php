<?php

namespace Gbrock\Table\Tests\Cases;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Gbrock\Table\Providers\TableServiceProvider;

abstract class TestCase extends BaseTestCase
{
    /**
     * Boots the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../vendor/laravel/laravel/bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        // Register our package's service provider
        $app->register(TableServiceProvider::class);

        // Register our package's facade
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Table', \Gbrock\Table\Facades\Table::class);

        return $app;
    }
}
