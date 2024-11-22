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
use NexaMerchant\CheckoutCod\Http\Controllers\Api\V1\PushController;

Route::group(['middleware' => ['api','assign_request_id'], 'prefix' => 'api'], function () {
    
     Route::prefix('v1/checkoutcod')->group(function () {

        Route::controller(ExampleController::class)->prefix('example')->group(function () {

            Route::get('demo', 'demo')->name('checkoutcod.api.v1.example.demo');

        });

        Route::controller(ProductsController::class)->prefix('products')->group(function () {

            Route::get('details/{slug}', 'details')->name('checkoutcod.api.v1.products.details');

            // recommend products
            Route::get('recommend/{slug}', 'recommend')->name('checkoutcod.api.v1.products.recommend');

        });

        // Orders
        Route::controller(OrdersController::class)->prefix('orders')->group(function () {

            Route::post('create', 'create')->name('checkoutcod.api.v1.orders.create');

            Route::get('details/{order_id}', 'details')->name('checkoutcod.api.v1.orders.details');

            Route::get('list', 'list')->name('checkoutcod.api.v1.orders.list');

            Route::get('cancel/{order_id}', 'cancel')->name('checkoutcod.api.v1.orders.cancel');

            Route::get('confirm/{order_id}', 'confirm')->name('checkoutcod.api.v1.orders.confirm');

        });

        // Pusher
        Route::controller(PushController::class)->prefix('pusher')->group(function () {

            Route::post('send', 'send')->name('checkoutcod.api.v1.pusher.send');

        });

     });
});