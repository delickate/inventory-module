<?php

namespace Delickate\InventoryModule;

use Illuminate\Support\ServiceProvider;

class InventoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Module/Inventory' => app_path('Modules/Inventory'),
        ], 'inventory-module');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallInventoryModule::class,
            ]);
        }
    }

    public function register()
    {
        //
    }
}