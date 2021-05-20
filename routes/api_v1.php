<?php

use App\Http\Controllers\API\V1\ActivityCommentController;
use App\Http\Controllers\API\V1\ActivityController;
use App\Http\Controllers\API\V1\AddressController;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\CartController;
use App\Http\Controllers\API\V1\ChannelController;
use App\Http\Controllers\API\V1\CompanyController;
use App\Http\Controllers\API\V1\CustomerController;
use App\Http\Controllers\API\V1\DiscountController;
use App\Http\Controllers\API\V1\LeadController;
use App\Http\Controllers\API\V1\OrderController;
use App\Http\Controllers\API\V1\ProductCategoryController;
use App\Http\Controllers\API\V1\ProductController;
use App\Http\Controllers\API\V1\ProductTagController;
use App\Http\Controllers\API\V1\ProductUnitController;
use App\Http\Controllers\API\V1\QaMessageController;
use App\Http\Controllers\API\V1\QaTopicController;
use App\Http\Controllers\API\V1\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    /** Users */
    Route::get('/users/me', [UserController::class, 'me'])->name('users.me');
    Route::get('/users/supervisor', [UserController::class, 'supervisor'])->name('users.supervisor');
    Route::get('/users/supervised', [UserController::class, 'supervised'])->name('users.supervised');
    Route::put('/users/channel/{channel}', [UserController::class, 'channel'])->name('users.channel');
    Route::resource('users', UserController::class)->only(['index', 'show']);

    /** Companies and Channel*/
    Route::resource('companies', CompanyController::class)->only(['index', 'show']);
    Route::get('channels/default', [ChannelController::class, 'default'])->name('channels.default');
    Route::resource('channels', ChannelController::class)->only(['index', 'show']);

    /** Global data */
    Route::get('/customers/addresses/create', [CustomerController::class, 'createWithAddress'])->name('customers.addresses.create');
    Route::post('/customers/addresses', [CustomerController::class, 'storeWithAddress'])->name('customers.addresses.store');
    Route::get('/customers/{customer}/leads', [CustomerController::class, 'getCustomerLeads'])->name('customers.leads');
    Route::get('/customers/{customer}/activities', [CustomerController::class, 'getCustomerActivities'])->name('customers.activities');
    Route::resource('customers', CustomerController::class);
    Route::resource('addresses', AddressController::class);

    /** Tenanted data */
    Route::middleware(['default_tenant'])->group(function () {
        Route::resource('leads', LeadController::class);
        Route::get('/activities/{activity}/comments', [ActivityController::class, 'getActivityComments'])->name('activities.comments');
        Route::resource('activities', ActivityController::class);
        Route::resource('activity-comments', ActivityCommentController::class);


        Route::get('brands', [ProductController::class, 'brands'])->name('brands');
        Route::get('models', [ProductController::class, 'models'])->name('models');
        Route::get('models/{model}', [ProductController::class, 'model'])->name('models.get');
        Route::get('versions', [ProductController::class, 'versions'])->name('versions');
        Route::get('category-codes', [ProductController::class, 'categoryCodes'])->name('category-codes');
        Route::resource('products', ProductController::class)->only(['index', 'show']);


        Route::get('colours', [ProductUnitController::class, 'colours'])->name('colours');
        Route::get('coverings', [ProductUnitController::class, 'coverings'])->name('coverings');
        Route::resource('product-units', ProductUnitController::class)->only(['index', 'show']);
        Route::resource('product-tags', ProductTagController::class)->only(['index', 'show']);
        Route::resource('product-categories', ProductCategoryController::class)->only(['index', 'show']);

        Route::get('/qa-topics/{topic}/qa-messages', [QaTopicController::class, 'messages'])->name('qa-topics.qa-messages');
        Route::resource('qa-topics', QaTopicController::class);
        Route::resource('qa-messages', QaMessageController::class);

        Route::get('carts', [CartController::class, 'index'])->name('carts.index');
        Route::put('carts', [CartController::class, 'sync'])->name('carts.sync');
        Route::get('discounts', [DiscountController::class, 'index'])->name('discounts.index');
        Route::get('discounts/{code}', [DiscountController::class, 'discountGetByCode'])->name('discounts.code');

        Route::resource('orders', OrderController::class)->only(['store', 'show', 'index']);
        Route::post('/orders/preview', [OrderController::class, 'preview'])->name('orders.preview');

    });
});

Route::prefix('auth')->group(function () {
    Route::post('/token', [AuthController::class, 'token'])->name('auth.token');
});
