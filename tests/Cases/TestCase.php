<?php

namespace Gbrock\Table\Tests\Cases;

use Gbrock\Table\Tests\Providers\TableTestingServiceProvider;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Gbrock\Table\Providers\TableServiceProvider;
use Illuminate\Support\Facades\Crypt;

abstract class TestCase extends BaseTestCase
{
    protected $baseUrl = '';

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
        $app->register(TableTestingServiceProvider::class);

        // Register our package's facade
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();

        $loader->alias('Table', \Gbrock\Table\Facades\Table::class);

        $router = $app->make('router');

        $router->group(['namespace' => 'Gbrock\Tables\Tests\Http'], function ($router) {
            get('/', function () {
                return view('testing::games');
            });
        });

        // Set app configuration
        config([
            'app.key' => str_random(32),
        ]);

        return $app;
    }
}
