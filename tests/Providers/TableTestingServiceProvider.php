<?php namespace Gbrock\Table\Tests\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class TableTestingServiceProvider extends ServiceProvider {

    protected $namespace = 'Gbrock\Table\Tests\Http\Controllers';

    /**
     * Register bindings in the container.
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap the application files.
     * @return void
     */
    public function boot()
    {
        $root = __DIR__.'/../';

        // Load views
        $this->loadViewsFrom($root . 'resources/views', 'testing');
    }
}
