<?php

namespace Delickate\InventoryModule\Console;

use Illuminate\Console\Command;

class InstallInventoryModule extends Command
{
    protected $signature = 'inventory:install';

    protected $description = 'Install Inventory Module';

    public function handle()
    {
        $this->call('vendor:publish', [
            '--tag' => 'inventory-module'
        ]);

        $this->info('Inventory module installed successfully.');
    }
}