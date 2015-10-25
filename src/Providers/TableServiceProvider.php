<?php namespace Gbrock\Table\Providers;

use Gbrock\Table\Table;
use Illuminate\Support\ServiceProvider;

class TableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application files.
     * @return void
     */
    public function boot()
    {
        // Publish views
        $this->publishes([
            __DIR__ . '/../../resources/views' => base_path('resources/views/vendor/gbrock'),
        ]);

        // Publish configuration
        $this->publishes([
            __DIR__ . '/../../config/tables.php' => config_path('tables.php'),
        ]);
    }

    /**
     * Register bindings in the container.
     * @return void
     */
    public function register()
    {
        $this->app->singleton('table', function () {
            return new Table;
        });

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'table');

        // Merge user config, passing in our defaults
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/tables.php', 'gbrock-tables'
        );
    }
}
