<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\TableController;
use App\Http\Controllers\admin\ItemController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\StaffNotificationController;
use App\Models\Category;
use App\Models\Item;

Route::get('/', function () {
    return view('layouts.app');
});

Route::get('/customer', function () {
    return view('layouts.customer');
});

// category
Route::resource(
    'categories',
    CategoryController::class
)->except([
    'show',
    'create',
    'edit'
]);

// Table
Route::resource(
    'tables',
    TableController::class
)->except([
    'show',
    'create',
    'edit'
]);

Route::get(
    '/menu/table/{table}',
    function ($table) {

        return redirect(
            '/menu?table=' . $table
        );
    }
)->name('menu.table');


// item
Route::resource(
    'items',
    ItemController::class
);

Route::patch('items/{item}/availability', [ItemController::class, 'toggleAvailability'])
    ->name('items.toggleAvailability');

Route::get('/admin/menu', function () {
    $items = Item::with('category')->latest()->paginate(10);
    $categories = Category::all();

    return view('admin.menu.index', compact('items', 'categories'));
})->name('admin.menu.index');

// Order
Route::resource(
    'orders',
    OrderController::class
)->names('admin.orders');

Route::post(
    '/checkout/{table}',
    [OrderController::class, 'store']
);

Route::patch(
    '/orders/{order}/status',
    [OrderController::class, 'updateStatus']
)->name('admin.orders.updateStatus');

Route::get(
    '/orders/table/{table}/invoice',
    [OrderController::class, 'tableInvoice']
)->name('admin.orders.tableInvoice');

Route::get(
    '/admin/dashboard',
    [DashboardController::class, 'index']
)->name('admin.dashboard.index');

Route::get(
    '/admin/staff-notifications',
    [StaffNotificationController::class, 'index']
)->name('admin.staffNotifications.index');

Route::patch(
    '/admin/staff-notifications/{notification}/read',
    [StaffNotificationController::class, 'markAsRead']
)->name('admin.staffNotifications.markRead');

use App\Http\Controllers\customer\CustomerController;

Route::get(
    '/menu',
    [CustomerController::class, 'index']
);

Route::get(
    '/menu/{table}',
    [CustomerController::class, 'index']
)->name('menu.table');

Route::get(
    '/menu/{table}/category/{category}',
    [CustomerController::class, 'index']
)->name('menu.category');

Route::get(
    '/cart/{table}',
    [CustomerController::class, 'cart']
)->name('cart.show');

Route::get(
    '/order-success/{order}',
    [CustomerController::class, 'success']
)->name('orders.success');

Route::get(
    'customer/orders/{table}',
    [CustomerController::class, 'orders']
)->name('customer.orders');

Route::get(
    '/track-order/{order}',
    [CustomerController::class, 'trackOrder']
)->name('track.order');

Route::post(
    '/track-order/{order}/contact',
    [CustomerController::class, 'contactStaff']
)->name('customer.contactStaff');

Route::get(
    '/order-status/{order}',
    [CustomerController::class, 'status']
)->name('order.status');
