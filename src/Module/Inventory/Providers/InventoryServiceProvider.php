<?php

namespace Modules\Inventory\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class InventoryServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Inventory';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'inventory';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        $helper = module_path('Inventory', 'Helpers/helpers.php');

        if (file_exists($helper)) {
            require_once $helper;
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);




        # inventory
        $this->app->bind(\Modules\Inventory\Repositories\Interfaces\ProductsRepositoryInterface::class, 
                         \Modules\Inventory\Repositories\ProductsRepository::class);
        $this->app->bind(\Modules\Inventory\Repositories\Interfaces\CategoriesRepositoryInterface::class, 
                         \Modules\Inventory\Repositories\CategoriesRepository::class);
        $this->app->bind(\Modules\Inventory\Repositories\Interfaces\UnitsRepositoryInterface::class, 
                         \Modules\Inventory\Repositories\UnitsRepository::class);
        $this->app->bind(\Modules\Inventory\Repositories\Interfaces\WarehousesRepositoryInterface::class, 
                         \Modules\Inventory\Repositories\WarehousesRepository::class);
        $this->app->bind(\Modules\Inventory\Repositories\Interfaces\InventoryRepositoryInterface::class, 
                         \Modules\Inventory\Repositories\InventoryRepository::class);
        $this->app->bind(\Modules\Inventory\Repositories\Interfaces\Stock_MovementsRepositoryInterface::class, 
                         \Modules\Inventory\Repositories\Stock_MovementsRepository::class);
        $this->app->bind(\Modules\Inventory\Repositories\Interfaces\SuppliersRepositoryInterface::class, 
                         \Modules\Inventory\Repositories\SuppliersRepository::class);
        $this->app->bind(\Modules\Inventory\Repositories\Interfaces\PurchasesRepositoryInterface::class, 
                         \Modules\Inventory\Repositories\PurchasesRepository::class);
        $this->app->bind(\Modules\Inventory\Repositories\Interfaces\Purchase_ItemsRepositoryInterface::class, 
                         \Modules\Inventory\Repositories\Purchase_ItemsRepository::class);
        $this->app->bind(\Modules\Inventory\Repositories\Interfaces\CustomersRepositoryInterface::class, 
                         \Modules\Inventory\Repositories\CustomersRepository::class);
        $this->app->bind(\Modules\Inventory\Repositories\Interfaces\SalesRepositoryInterface::class, 
                         \Modules\Inventory\Repositories\SalesRepository::class);
        $this->app->bind(\Modules\Inventory\Repositories\Interfaces\Sale_ItemsRepositoryInterface::class, 
                         \Modules\Inventory\Repositories\Sale_ItemsRepository::class);
        $this->app->bind(\Modules\Inventory\Repositories\Interfaces\Purchase_ReturnsRepositoryInterface::class, 
                         \Modules\Inventory\Repositories\Purchase_ReturnsRepository::class);
        $this->app->bind(\Modules\Inventory\Repositories\Interfaces\Purchase_Return_ItemsRepositoryInterface::class, 
                         \Modules\Inventory\Repositories\Purchase_Return_ItemsRepository::class);

    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
