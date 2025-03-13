<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\ShopifyProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::middleware(['verify.shopify'])->group(function () {
    // Route::get('/', [RuleController::class, 'index'])->name('home');
    // Route::get('/rule/create', [RuleController::class, 'create'])->name('create.rule');

    // Route::post('/rule/store', [RuleController::class, 'store']);

    // Route::get('/rule/{rule}/edit', [RuleController::class, 'edit'])->name('rule.edit');
    
    // Route::post('/rule/update/{rule}', [RuleController::class, 'update']);
    // Route::POST('/rule/{rule}', [RuleController::class, 'destroy']);




    //shopify api routes
    Route::get('/', [ShopifyProductController::class, 'getProducts'])->name('home');

    Route::get('/create', [ShopifyProductController::class, 'create'])->name('create.product');
    Route::post('/create', [ShopifyProductController::class, 'store'])->name('product.store');

    Route::post('/delete-product', [ShopifyProductController::class, 'deleteProduct']);


});


