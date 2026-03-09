<?php

use Illuminate\Support\Facades\Route;



#inventory routes
use Modules\Inventory\Http\Controllers\ProductsController;
use Modules\Inventory\Http\Controllers\CategoriesController;
use Modules\Inventory\Http\Controllers\UnitsController;
use Modules\Inventory\Http\Controllers\WarehousesController;
use Modules\Inventory\Http\Controllers\InventoryController;
use Modules\Inventory\Http\Controllers\Stock_MovementsController;
use Modules\Inventory\Http\Controllers\SuppliersController;
use Modules\Inventory\Http\Controllers\PurchasesController;
use Modules\Inventory\Http\Controllers\Purchase_ItemsController;
use Modules\Inventory\Http\Controllers\Purchase_ReturnsController;
use Modules\Inventory\Http\Controllers\Purchase_Return_ItemsController;
use Modules\Inventory\Http\Controllers\CustomersController;
use Modules\Inventory\Http\Controllers\SalesController;
use Modules\Inventory\Http\Controllers\Sale_ItemsController;
use Modules\Inventory\Http\Controllers\Sale_ReturnsController;

#reports
use Modules\Inventory\Http\Controllers\InventoryReportsController;

//auto vouching
use Modules\Inventory\Http\Controllers\AutoVouchingInventoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('inventory')->group(function() 
{
    Route::get('/', 'InventoryController@index');

    Route::group(['prefix'=>'suppliers/','as'=>'suppliers.'], function()
    {
       Route::get('listing', [SuppliersController::class, 'index'])->name('listing');
       Route::get('adding', [SuppliersController::class, 'create'])->name('adding');
       Route::post('saving', [SuppliersController::class, 'store'])->name('saving');

       Route::get('editing/{id}', [SuppliersController::class, 'edit'])->name('editing');
       Route::get('showing/{id}', [SuppliersController::class, 'show'])->name('showing');
       Route::post('updating/{id}', [SuppliersController::class, 'update'])->name('updating');

       Route::get('deleting/{id}', [SuppliersController::class, 'destroy'])->name('deleting');
    }); 
      
    Route::group(['prefix'=>'products/','as'=>'products.'], function()
    {
       Route::get('listing', [ProductsController::class, 'index'])->name('listing');
       Route::get('adding', [ProductsController::class, 'create'])->name('adding');
       Route::post('saving', [ProductsController::class, 'store'])->name('saving');

       Route::get('editing/{id}', [ProductsController::class, 'edit'])->name('editing');
       Route::get('showing/{id}', [ProductsController::class, 'show'])->name('showing');
       Route::post('updating/{id}', [ProductsController::class, 'update'])->name('updating');

       Route::get('deleting/{id}', [ProductsController::class, 'destroy'])->name('deleting');
    });


    Route::group(['prefix'=>'categories/','as'=>'categories.'], function()
    {
       Route::get('listing', [CategoriesController::class, 'index'])->name('listing');
       Route::get('adding', [CategoriesController::class, 'create'])->name('adding');
       Route::post('saving', [CategoriesController::class, 'store'])->name('saving');

       Route::get('editing/{id}', [CategoriesController::class, 'edit'])->name('editing');
       Route::get('showing/{id}', [CategoriesController::class, 'show'])->name('showing');
       Route::post('updating/{id}', [CategoriesController::class, 'update'])->name('updating');

       Route::get('deleting/{id}', [CategoriesController::class, 'destroy'])->name('deleting');
    }); 

    Route::group(['prefix'=>'units/','as'=>'units.'], function()
    {
       Route::get('listing', [UnitsController::class, 'index'])->name('listing');
       Route::get('adding', [UnitsController::class, 'create'])->name('adding');
       Route::post('saving', [UnitsController::class, 'store'])->name('saving');

       Route::get('editing/{id}', [UnitsController::class, 'edit'])->name('editing');
       Route::get('showing/{id}', [UnitsController::class, 'show'])->name('showing');
       Route::post('updating/{id}', [UnitsController::class, 'update'])->name('updating');

       Route::get('deleting/{id}', [UnitsController::class, 'destroy'])->name('deleting');
    }); 

    Route::group(['prefix'=>'warehouses/','as'=>'warehouses.'], function()
    {
       Route::get('listing', [WarehousesController::class, 'index'])->name('listing');
       Route::get('adding', [WarehousesController::class, 'create'])->name('adding');
       Route::post('saving', [WarehousesController::class, 'store'])->name('saving');

       Route::get('editing/{id}', [WarehousesController::class, 'edit'])->name('editing');
       Route::get('showing/{id}', [WarehousesController::class, 'show'])->name('showing');
       Route::post('updating/{id}', [WarehousesController::class, 'update'])->name('updating');

       Route::get('deleting/{id}', [WarehousesController::class, 'destroy'])->name('deleting');
    });

    Route::group(['prefix'=>'inventory/','as'=>'inventory.'], function()
    {
       Route::get('listing', [InventoryController::class, 'index'])->name('listing');
    });


    Route::group(['prefix'=>'stock_movements/','as'=>'stock_movements.'], function()
    {
       Route::get('listing', [Stock_MovementsController::class, 'index'])->name('listing');
    }); 





    Route::group(['prefix'=>'purchases/','as'=>'purchases.'], function()
    {
       Route::get('listing', [PurchasesController::class, 'index'])->name('listing');
       Route::get('adding', [PurchasesController::class, 'create'])->name('adding');
       Route::post('saving', [PurchasesController::class, 'store'])->name('saving');

       Route::get('editing/{id}', [PurchasesController::class, 'edit'])->name('editing');
       Route::get('showing/{id}', [PurchasesController::class, 'show'])->name('showing');
       Route::post('updating/{id}', [PurchasesController::class, 'update'])->name('updating');

       Route::get('deleting/{id}', [PurchasesController::class, 'destroy'])->name('deleting');


       //approve PO
       Route::post('/{id}/approve', [PurchaseController::class, 'approvePurchase'])
         ->name('purchases.approve');
         //->middleware(['auth', 'can:approve_purchases']);

         
         Route::get('returnForm/{id}', [PurchasesController::class, 'showReturnForm'])->name('returnForm');

         
         //->middleware('auth');

        Route::post('returnProcess/{id}', [PurchasesController::class, 'processReturn'])
         ->name('returnProcess');
         //->middleware('auth');
    }); 


   

    Route::group(['prefix'=>'customers/','as'=>'customers.'], function()
    {
       Route::get('listing', [CustomersController::class, 'index'])->name('listing');
       Route::get('adding', [CustomersController::class, 'create'])->name('adding');
       Route::post('saving', [CustomersController::class, 'store'])->name('saving');

       Route::get('editing/{id}', [CustomersController::class, 'edit'])->name('editing');
       Route::get('showing/{id}', [CustomersController::class, 'show'])->name('showing');
       Route::post('updating/{id}', [CustomersController::class, 'update'])->name('updating');

       Route::get('deleting/{id}', [CustomersController::class, 'destroy'])->name('deleting');
    });  
             


    Route::group(['prefix'=>'sales/','as'=>'sales.'], function()
    {
       Route::get('listing', [SalesController::class, 'index'])->name('listing');
       Route::get('adding', [SalesController::class, 'create'])->name('adding');
       Route::post('saving', [SalesController::class, 'store'])->name('saving');

       Route::get('editing/{id}', [SalesController::class, 'edit'])->name('editing');
       Route::get('showing/{id}', [SalesController::class, 'show'])->name('showing');
       Route::post('updating/{id}', [SalesController::class, 'update'])->name('updating');

       Route::get('deleting/{id}', [SalesController::class, 'destroy'])->name('deleting');


       Route::get('returnForm/{id}', [SalesController::class, 'showReturnForm'])->name('returnForm');
         
       Route::post('returnProcess/{id}', [SalesController::class, 'processReturn'])
         ->name('returnProcess');

    });  
             


             

      Route::group(['prefix'=>'warehouses/','as'=>'warehouses.'], function()
       {
          Route::get('listing', [WarehousesController::class, 'index'])->name('listing');
          Route::get('adding', [WarehousesController::class, 'create'])->name('adding');
          Route::post('saving', [WarehousesController::class, 'store'])->name('saving');

          Route::get('editing/{id}', [WarehousesController::class, 'edit'])->name('editing');
          Route::get('showing/{id}', [WarehousesController::class, 'show'])->name('showing');
          Route::post('updating/{id}', [WarehousesController::class, 'update'])->name('updating');

          Route::get('deleting/{id}', [WarehousesController::class, 'destroy'])->name('deleting');
       });    
           


      Route::group(['prefix'=>'inventoryReports/','as'=>'inventoryReports.'], function()
       {
          Route::get('CurrentStockStatus', [InventoryReportsController::class, 'CurrentStockStatus'])->name('CurrentStockStatus');
          Route::get('LowStockAlert', [InventoryReportsController::class, 'LowStockAlert'])->name('LowStockAlert');
          Route::get('SalesSummary', [InventoryReportsController::class, 'SalesSummary'])->name('SalesSummary');
          Route::get('CustomerPurchaseHistory', [InventoryReportsController::class, 'CustomerPurchaseHistory'])->name('CustomerPurchaseHistory');
          Route::get('PurchaseSummary', [InventoryReportsController::class, 'PurchaseSummary'])->name('PurchaseSummary');
          Route::get('ProfitNLoss', [InventoryReportsController::class, 'ProfitNLoss'])->name('ProfitNLoss');
          Route::get('AccountsPayable', [InventoryReportsController::class, 'AccountsPayable'])->name('AccountsPayable');
          Route::get('AccountsReceivable', [InventoryReportsController::class, 'AccountsReceivable'])->name('AccountsReceivable');
       }); 


      Route::prefix('AutoVouchingInventory')->name('AutoVouchingInventory.')->group(function () 
      {
        Route::get('listing', [AutoVouchingInventoryController::class, 'index'])->name('listing');
        Route::get('edit/{id}', [AutoVouchingInventoryController::class, 'edit'])->name('edit');
        Route::post('update/{id}', [AutoVouchingInventoryController::class, 'update'])->name('update');
    });

});
