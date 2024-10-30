<?php
/**
 * 
 * This file is auto generate by Nicelizhi\Apps\Commands\Create
 * @author Steve
 * @date 2024-10-29 19:01:49
 * @link https://github.com/xxxl4
 * 
 */
use Illuminate\Support\Facades\Route;
use NexaMerchant\CheckoutCod\Http\Controllers\Api\V1\ExampleController;
use NexaMerchant\CheckoutCod\Http\Controllers\Api\V1\ProductsController;
use NexaMerchant\CheckoutCod\Http\Controllers\Api\V1\OrdersController;

Route::group(['middleware' => ['api'], 'prefix' => 'api'], function () {
    
     Route::prefix('v1/checkoutcod')->group(function () {

        Route::controller(ExampleController::class)->prefix('example')->group(function () {

            Route::get('demo', 'demo')->name('checkoutcod.api.example.demo');

        });

        Route::controller(ProductsController::class)->prefix('products')->group(function () {

            Route::get('details/{slug}', 'details')->name('checkoutcod.api.products.details');

            // recommend products
            Route::get('recommend/{slug}', 'recommend')->name('checkoutcod.api.products.recommend');

        });

     });
});