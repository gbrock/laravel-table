<?php namespace Gbrock\Table\Providers;

use Gbrock\Table\Table;
use Illuminate\Support\ServiceProvider;

class TableServiceProvider extends ServiceProvider {

    /**
     * Register bindings in the container.
     * @return void
     */
    public function register()
    {
        $this->app->bind('table', function()
        {
            return new Table;
        });
    }

    /**
     * Bootstrap the application files.
     * @return void
     */
    public function boot()
    {
        $root = __DIR__.'/../../';
        // Load views
        $this->loadViewsFrom($root . 'resources/views', 'gbrock');

        // Publish views
        $this->publishes([
            $root . 'resources/views' => base_path('resources/views/vendor/gbrock'),
        ]);

        // Publish configuration
        $this->publishes([
            $root . 'config/tables.php' => config_path('gbrock-tables.php'),
        ]);

        // Merge user config, passing in our defaults
        $this->mergeConfigFrom(
            $root . 'config/tables.php', 'gbrock-tables'
        );

        // Publish assets
//        $this->publishes([
//            $root . 'build/assets' => public_path('vendor/gbrock/tables'),
//        ], 'public');
    }
}
