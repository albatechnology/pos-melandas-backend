<?php

use App\Http\Controllers\Admin\ActivityCommentController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CatalogueController;
use App\Http\Controllers\Admin\ChannelCategoryController;
use App\Http\Controllers\Admin\ChannelController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ImportBatchesController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\ItemProductUnitController;
use App\Http\Controllers\Admin\LeadsController;
use App\Http\Controllers\Admin\MessengerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderDetailController;
use App\Http\Controllers\Admin\PaymentCategoryController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentTypeController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductTagController;
use App\Http\Controllers\Admin\ProductUnitsController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\ShipmentController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\SupervisorTypeController;
use App\Http\Controllers\Admin\TargetController;
use App\Http\Controllers\Admin\TargetScheduleController;
use App\Http\Controllers\Admin\TaxInvoiceController;
use App\Http\Controllers\Admin\UserAlertsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Auth\ChangePasswordController;
use Illuminate\Support\Facades\Route;


Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('/tenants/active', [HomeController::class, 'setActiveTenant'])->name('tenants.active');

    // Permissions
    Route::delete('permissions/destroy', [PermissionsController::class, 'massDestroy'])->name('permissions.massDestroy');
    Route::post('permissions/parse-csv-import', [PermissionsController::class, 'parseCsvImport'])->name('permissions.parseCsvImport');
    Route::post('permissions/process-csv-import', [PermissionsController::class, 'processCsvImport'])->name('permissions.processCsvImport');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', [RolesController::class, 'massDestroy'])->name('roles.massDestroy');
    Route::post('roles/parse-csv-import', [RolesController::class, 'parseCsvImport'])->name('roles.parseCsvImport');
    Route::post('roles/process-csv-import', [RolesController::class, 'processCsvImport'])->name('roles.processCsvImport');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', [UsersController::class, 'massDestroy'])->name('users.massDestroy');
    Route::post('users/parse-csv-import', [UsersController::class, 'parseCsvImport'])->name('users.parseCsvImport');
    Route::post('users/process-csv-import', [UsersController::class, 'processCsvImport'])->name('users.processCsvImport');
    Route::resource('users', 'UsersController');

    // Product Categories
    Route::delete('product-categories/destroy', [ProductCategoryController::class, 'massDestroy'])->name('product-categories.massDestroy');
    Route::post('product-categories/media', [ProductCategoryController::class, 'storeMedia'])->name('product-categories.storeMedia');
    Route::post('product-categories/ckmedia', [ProductCategoryController::class, 'storeCKEditorImages'])->name('product-categories.storeCKEditorImages');
    Route::post('product-categories/parse-csv-import', [ProductCategoryController::class, 'parseCsvImport'])->name('product-categories.parseCsvImport');
    Route::post('product-categories/process-csv-import', [ProductCategoryController::class, 'processCsvImport'])->name('product-categories.processCsvImport');
    Route::resource('product-categories', 'ProductCategoryController');

    // Product Tags
    Route::delete('product-tags/destroy', [ProductTagController::class, 'massDestroy'])->name('product-tags.massDestroy');
    Route::post('product-tags/parse-csv-import', [ProductTagController::class, 'parseCsvImport'])->name('product-tags.parseCsvImport');
    Route::post('product-tags/process-csv-import', [ProductTagController::class, 'processCsvImport'])->name('product-tags.processCsvImport');
    Route::resource('product-tags', 'ProductTagController');

    // Products
    Route::delete('products/destroy', [ProductController::class, 'massDestroy'])->name('products.massDestroy');
    Route::post('products/media', [ProductController::class, 'storeMedia'])->name('products.storeMedia');
    Route::post('products/ckmedia', [ProductController::class, 'storeCKEditorImages'])->name('products.storeCKEditorImages');
    Route::post('products/parse-csv-import', [ProductController::class, 'parseCsvImport'])->name('products.parseCsvImport');
    Route::post('products/process-csv-import', [ProductController::class, 'processCsvImport'])->name('products.processCsvImport');
    Route::resource('products', 'ProductController');

    // Product Units
    Route::delete('product-units/destroy', [ProductUnitsController::class, 'massDestroy'])->name('product-units.massDestroy');
    Route::post('product-units/media', [ProductUnitsController::class, 'storeMedia'])->name('product-units.storeMedia');
    Route::post('product-units/ckmedia', [ProductUnitsController::class, 'storeCKEditorImages'])->name('product-units.storeCKEditorImages');
    Route::post('product-units/parse-csv-import', [ProductUnitsController::class, 'parseCsvImport'])->name('product-units.parseCsvImport');
    Route::post('product-units/process-csv-import', [ProductUnitsController::class, 'processCsvImport'])->name('product-units.processCsvImport');
    Route::resource('product-units', 'ProductUnitsController');

    // Imports
    Route::get('import-batches/{id}/ajax/status/', [ImportBatchesController::class, 'ajaxStatus'])->name('import-batches.ajax.status');
    Route::get('import-batches/{id}/ajax/count/{batch_status}', [ImportBatchesController::class, 'ajaxLineCount'])->name('import-batches.ajax.count');
    Route::get('import-batches/{import_batch}/process-update', [ImportBatchesController::class, 'processUpdate'])->name('import-batches.processUpdate');
    Route::get('import-batches/{import_batch}/process-skip', [ImportBatchesController::class, 'processSkip'])->name('import-batches.processSkip');
    Route::get('import-batches/{import_batch}/process-cancel', [ImportBatchesController::class, 'processCancel'])->name('import-batches.processCancel');
    Route::post('import-batches/media', [ProductController::class, 'storeMedia'])->name('import-batches.storeMedia');
    Route::resource('import-batches', 'ImportBatchesController');
    Route::resource('import-batches.import-lines', 'ImportLinesController');

    // Exports
    Route::get('exports/sample/{type}', [ExportController::class, 'sample'])->name('exports.sample');


    // Companies
    Route::delete('companies/destroy', [CompanyController::class, 'massDestroy'])->name('companies.massDestroy');
    Route::resource('companies', 'CompanyController');

    // Channel Categories
    Route::delete('channel-categories/destroy', [ChannelCategoryController::class, 'massDestroy'])->name('channel-categories.massDestroy');
    Route::resource('channel-categories', 'ChannelCategoryController');

    // Channels
    Route::delete('channels/destroy', [ChannelController::class, 'massDestroy'])->name('channels.massDestroy');
    Route::resource('channels', 'ChannelController');

    // Leads
    Route::delete('leads/destroy', [LeadsController::class, 'massDestroy'])->name('leads.massDestroy');
    Route::post('leads/parse-csv-import', [LeadsController::class, 'parseCsvImport'])->name('leads.parseCsvImport');
    Route::post('leads/process-csv-import', [LeadsController::class, 'processCsvImport'])->name('leads.processCsvImport');
    Route::resource('leads', 'LeadsController');

    // Activities
    Route::delete('activities/destroy', [ActivityController::class, 'massDestroy'])->name('activities.massDestroy');
    Route::post('activities/parse-csv-import', [ActivityController::class, 'parseCsvImport'])->name('activities.parseCsvImport');
    Route::post('activities/process-csv-import', [ActivityController::class, 'processCsvImport'])->name('activities.processCsvImport');
    Route::resource('activities', 'ActivityController');

    // Items
    Route::delete('items/destroy', [ItemController::class, 'massDestroy'])->name('items.massDestroy');
    Route::post('items/parse-csv-import', [ItemController::class, 'parseCsvImport'])->name('items.parseCsvImport');
    Route::post('items/process-csv-import', [ItemController::class, 'processCsvImport'])->name('items.processCsvImport');
    Route::resource('items', 'ItemController');

    // Item Product Units
    Route::delete('item-product-units/destroy', [ItemProductUnitController::class, 'massDestroy'])->name('item-product-units.massDestroy');
    Route::post('item-product-units/parse-csv-import', [ItemProductUnitController::class, 'parseCsvImport'])->name('item-product-units.parseCsvImport');
    Route::post('item-product-units/process-csv-import', [ItemProductUnitController::class, 'processCsvImport'])->name('item-product-units.processCsvImport');
    Route::resource('item-product-units', 'ItemProductUnitController');

    // Customers
    Route::delete('customers/destroy', [CustomerController::class, 'massDestroy'])->name('customers.massDestroy');
    Route::post('customers/parse-csv-import', [CustomerController::class, 'parseCsvImport'])->name('customers.parseCsvImport');
    Route::post('customers/process-csv-import', [CustomerController::class, 'processCsvImport'])->name('customers.processCsvImport');
    Route::resource('customers', 'CustomerController');

    // Audit Logs
    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    // Addresses
    Route::delete('addresses/destroy', [AddressController::class, 'massDestroy'])->name('addresses.massDestroy');
    Route::post('addresses/parse-csv-import', [AddressController::class, 'parseCsvImport'])->name('addresses.parseCsvImport');
    Route::post('addresses/process-csv-import', [AddressController::class, 'processCsvImport'])->name('addresses.processCsvImport');
    Route::resource('addresses', 'AddressController');

    // Reports
    Route::resource('reports', 'ReportController', ['except' => ['create', 'store', 'edit', 'update', 'show', 'destroy']]);

    // Notifications
    Route::resource('notifications', 'NotificationController', ['except' => ['create', 'store', 'edit', 'update', 'show', 'destroy']]);

    // Discounts
    Route::delete('discounts/destroy', [DiscountController::class, 'massDestroy'])->name('discounts.massDestroy');
    Route::resource('discounts', 'DiscountController');

    // User Alerts
    Route::delete('user-alerts/destroy', [UserAlertsController::class, 'massDestroy'])->name('user-alerts.massDestroy');
    Route::resource('user-alerts', 'UserAlertsController', ['except' => ['edit', 'update']]);

    // Promos
    Route::delete('promos/destroy', [PromoController::class, 'massDestroy'])->name('promos.massDestroy');
    Route::post('promos/media', [PromoController::class, 'storeMedia'])->name('promos.storeMedia');
    Route::post('promos/ckmedia', [PromoController::class, 'storeCKEditorImages'])->name('promos.storeCKEditorImages');
    Route::resource('promos', 'PromoController');

    // Banners
    Route::delete('banners/destroy', [BannerController::class, 'massDestroy'])->name('banners.massDestroy');
    Route::resource('banners', 'BannerController');

    // Payment Categories
    Route::delete('payment-categories/destroy', [PaymentCategoryController::class, 'massDestroy'])->name('payment-categories.massDestroy');
    Route::resource('payment-categories', 'PaymentCategoryController');

    // Payment Types
    Route::delete('payment-types/destroy', [PaymentTypeController::class, 'massDestroy'])->name('payment-types.massDestroy');
    Route::resource('payment-types', 'PaymentTypeController');

    // Activity Comments
    Route::delete('activity-comments/destroy', [ActivityCommentController::class, 'massDestroy'])->name('activity-comments.massDestroy');
    Route::post('activity-comments/parse-csv-import', [ActivityCommentController::class, 'parseCsvImport'])->name('activity-comments.parseCsvImport');
    Route::post('activity-comments/process-csv-import', [ActivityCommentController::class, 'processCsvImport'])->name('activity-comments.processCsvImport');
    Route::resource('activity-comments', 'ActivityCommentController');

    // Orders
    Route::delete('orders/destroy', [OrderController::class, 'massDestroy'])->name('orders.massDestroy');
    Route::resource('orders', 'OrderController', ['except' => ['create', 'store']]);

    // Order Trackings
    Route::resource('order-trackings', 'OrderTrackingController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    // Tax Invoices
    Route::delete('tax-invoices/destroy', [TaxInvoiceController::class, 'massDestroy'])->name('tax-invoices.massDestroy');
    Route::resource('tax-invoices', 'TaxInvoiceController');

    // Order Details
    Route::post('order-details/media', [OrderDetailController::class, 'storeMedia'])->name('order-details.storeMedia');
    Route::post('order-details/ckmedia', [OrderDetailController::class, 'storeCKEditorImages'])->name('order-details.storeCKEditorImages');
    Route::resource('order-details', 'OrderDetailController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    // Payments
    Route::delete('payments/destroy', [PaymentController::class, 'massDestroy'])->name('payments.massDestroy');
    Route::post('payments/media', [PaymentController::class, 'storeMedia'])->name('payments.storeMedia');
    Route::post('payments/ckmedia', [PaymentController::class, 'storeCKEditorImages'])->name('payments.storeCKEditorImages');
    Route::resource('payments', 'PaymentController');

    // Shipments
    Route::delete('shipments/destroy', [ShipmentController::class, 'massDestroy'])->name('shipments.massDestroy');
    Route::resource('shipments', 'ShipmentController');

    // Invoices
    Route::delete('invoices/destroy', [InvoiceController::class, 'massDestroy'])->name('invoices.massDestroy');
    Route::post('invoices/media', [InvoiceController::class, 'storeMedia'])->name('invoices.storeMedia');
    Route::post('invoices/ckmedia', [InvoiceController::class, 'storeCKEditorImages'])->name('invoices.storeCKEditorImages');
    Route::resource('invoices', 'InvoiceController');

    // Targets
    Route::delete('targets/destroy', [TargetController::class, 'massDestroy'])->name('targets.massDestroy');
    Route::post('targets/parse-csv-import', [TargetController::class, 'parseCsvImport'])->name('targets.parseCsvImport');
    Route::post('targets/process-csv-import', [TargetController::class, 'processCsvImport'])->name('targets.processCsvImport');
    Route::resource('targets', 'TargetController');

    // Catalogues
    Route::delete('catalogues/destroy', [CatalogueController::class, 'massDestroy'])->name('catalogues.massDestroy');
    Route::post('catalogues/media', [CatalogueController::class, 'storeMedia'])->name('catalogues.storeMedia');
    Route::post('catalogues/ckmedia', [CatalogueController::class, 'storeCKEditorImages'])->name('catalogues.storeCKEditorImages');
    Route::post('catalogues/parse-csv-import', [CatalogueController::class, 'parseCsvImport'])->name('catalogues.parseCsvImport');
    Route::post('catalogues/process-csv-import', [CatalogueController::class, 'processCsvImport'])->name('catalogues.processCsvImport');
    Route::resource('catalogues', 'CatalogueController');

    // Stocks
    Route::post('stocks/parse-csv-import', [StockController::class, 'parseCsvImport'])->name('stocks.parseCsvImport');
    Route::post('stocks/process-csv-import', [StockController::class, 'processCsvImport'])->name('stocks.processCsvImport');
    Route::resource('stocks', 'StockController', ['except' => ['create', 'store', 'destroy']]);

    // Stock Transfers
    Route::resource('stock-transfers', 'StockTransferController', ['except' => ['edit', 'update', 'destroy']]);

    // Supervisor Types
    Route::delete('supervisor-types/destroy', [SupervisorTypeController::class, 'massDestroy'])->name('supervisor-types.massDestroy');
    Route::resource('supervisor-types', 'SupervisorTypeController');

    // Target Schedules
    Route::delete('target-schedules/destroy', [TargetScheduleController::class, 'massDestroy'])->name('target-schedules.massDestroy');
    Route::resource('target-schedules', 'TargetScheduleController');

    // Product Brand
    Route::delete('product-brands/destroy', 'ProductBrandController@massDestroy')->name('product-brands.massDestroy');
    Route::post('product-brands/media', 'ProductBrandController@storeMedia')->name('product-brands.storeMedia');
    Route::post('product-brands/ckmedia', 'ProductBrandController@storeCKEditorImages')->name('product-brands.storeCKEditorImages');
    Route::post('product-brands/parse-csv-import', 'ProductBrandController@parseCsvImport')->name('product-brands.parseCsvImport');
    Route::post('product-brands/process-csv-import', 'ProductBrandController@processCsvImport')->name('product-brands.processCsvImport');
    Route::resource('product-brands', 'ProductBrandController');

    // Product Model
    Route::delete('product-models/destroy', 'ProductModelController@massDestroy')->name('product-models.massDestroy');
    Route::post('product-models/media', 'ProductModelController@storeMedia')->name('product-models.storeMedia');
    Route::post('product-models/ckmedia', 'ProductModelController@storeCKEditorImages')->name('product-models.storeCKEditorImages');
    Route::post('product-models/parse-csv-import', 'ProductModelController@parseCsvImport')->name('product-models.parseCsvImport');
    Route::post('product-models/process-csv-import', 'ProductModelController@processCsvImport')->name('product-models.processCsvImport');
    Route::resource('product-models', 'ProductModelController');

    // Product Version
    Route::delete('product-versions/destroy', 'ProductVersionController@massDestroy')->name('product-versions.massDestroy');
    Route::post('product-versions/media', 'ProductVersionController@storeMedia')->name('product-versions.storeMedia');
    Route::post('product-versions/ckmedia', 'ProductVersionController@storeCKEditorImages')->name('product-versions.storeCKEditorImages');
    Route::post('product-versions/parse-csv-import', 'ProductVersionController@parseCsvImport')->name('product-versions.parseCsvImport');
    Route::post('product-versions/process-csv-import', 'ProductVersionController@processCsvImport')->name('product-versions.processCsvImport');
    Route::resource('product-versions', 'ProductVersionController');

    // Product Category Code
    Route::delete('product-category-codes/destroy', 'ProductCategoryCodeController@massDestroy')->name('product-category-codes.massDestroy');
    Route::post('product-category-codes/parse-csv-import', 'ProductCategoryCodeController@parseCsvImport')->name('product-category-codes.parseCsvImport');
    Route::post('product-category-codes/process-csv-import', 'ProductCategoryCodeController@processCsvImport')->name('product-category-codes.processCsvImport');
    Route::resource('product-category-codes', 'ProductCategoryCodeController');

    // Covering
    Route::delete('coverings/destroy', 'CoveringController@massDestroy')->name('coverings.massDestroy');
    Route::post('coverings/media', 'CoveringController@storeMedia')->name('coverings.storeMedia');
    Route::post('coverings/ckmedia', 'CoveringController@storeCKEditorImages')->name('coverings.storeCKEditorImages');
    Route::post('coverings/parse-csv-import', 'CoveringController@parseCsvImport')->name('coverings.parseCsvImport');
    Route::post('coverings/process-csv-import', 'CoveringController@processCsvImport')->name('coverings.processCsvImport');
    Route::resource('coverings', 'CoveringController');

    // Colour
    Route::delete('colours/destroy', 'ColourController@massDestroy')->name('colours.massDestroy');
    Route::post('colours/media', 'ColourController@storeMedia')->name('colours.storeMedia');
    Route::post('colours/ckmedia', 'ColourController@storeCKEditorImages')->name('colours.storeCKEditorImages');
    Route::post('colours/parse-csv-import', 'ColourController@parseCsvImport')->name('colours.parseCsvImport');
    Route::post('colours/process-csv-import', 'ColourController@processCsvImport')->name('colours.processCsvImport');
    Route::resource('colours', 'ColourController');


    Route::get('messenger', [MessengerController::class, 'index'])->name('messenger.index');
    Route::get('messenger/create', [MessengerController::class, 'createTopic'])->name('messenger.createTopic');
    Route::post('messenger', [MessengerController::class, 'storeTopic'])->name('messenger.storeTopic');
    Route::get('messenger/inbox', [MessengerController::class, 'showInbox'])->name('messenger.showInbox');
    Route::get('messenger/outbox', [MessengerController::class, 'showOutbox'])->name('messenger.showOutbox');
    Route::get('messenger/{topic}', [MessengerController::class, 'showMessages'])->name('messenger.showMessages');
    Route::delete('messenger/{topic}', [MessengerController::class, 'destroyTopic'])->name('messenger.destroyTopic');
    Route::post('messenger/{topic}/reply', [MessengerController::class, 'replyToTopic'])->name('messenger.reply');
    Route::get('messenger/{topic}/reply', [MessengerController::class, 'showReply'])->name('messenger.showReply');
    Route::get('user-alerts/read', 'UserAlertsController@read');
});

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
// Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', [ChangePasswordController::class, 'edit'])->name('password.edit');
        Route::post('password', [ChangePasswordController::class, 'update'])->name('password.update');
        Route::post('profile', [ChangePasswordController::class, 'updateProfile'])->name('password.updateProfile');
        Route::post('profile/destroy', [ChangePasswordController::class, 'destroy'])->name('password.destroyProfile');
    }
});
