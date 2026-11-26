<?php

use App\Http\Controllers\Admin\{
    ClienteController,
    CompraController,
    DetalleCompraController,
    MesaController,
    OrdenController,
    ProveedorController,
    MenuProductController,
    UserManagementController,
};
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Restaurant\ComandaController;
use App\Http\Controllers\Restaurant\KitchenDisplayController;
use App\Http\Controllers\Restaurant\PointOfSaleController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Rutas generales
Route::get('/', fn() => view('welcome'))->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:dashboard.view'])
    ->name('dashboard');

Route::middleware(['auth', 'permission:pos.access'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PointOfSaleController::class, 'index'])->name('index');
    Route::post('/cash/open', [PointOfSaleController::class, 'openCashSession'])
        ->middleware('permission:cash.manage')->name('cash.open');
    Route::post('/cash/close', [PointOfSaleController::class, 'closeCashSession'])
        ->middleware('permission:cash.manage')->name('cash.close');
    Route::post('/orders', [PointOfSaleController::class, 'storeOrder'])->name('orders.store');
    Route::post('/orders/{order}/payments', [PointOfSaleController::class, 'storePayment'])
        ->middleware('permission:cash.manage')->name('orders.payments.store');
});

Route::middleware(['auth', 'permission:comandas.view'])->prefix('comandas')->name('comandas.')->group(function () {
    Route::get('/', [ComandaController::class, 'index'])->name('index');
    Route::post('/orders', [ComandaController::class, 'store'])
        ->middleware('permission:comandas.create')->name('orders.store');
    Route::post('/orders/{order}/served', [ComandaController::class, 'markServed'])
        ->middleware('permission:comandas.update')->name('orders.served');
});

Route::middleware(['auth', 'permission:kds.view'])->prefix('kds')->name('kds.')->group(function () {
    Route::get('/', [KitchenDisplayController::class, 'index'])->name('index');
});

// Rutas de configuración
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Rutas de administración protegidas
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('customers', [ClienteController::class, 'index'])->middleware('permission:customers.view')->name('customers.index');
    Route::post('customers', [ClienteController::class, 'store'])->middleware('permission:customers.create')->name('customers.store');
    Route::put('customers/{customer}', [ClienteController::class, 'update'])->middleware('permission:customers.update')->name('customers.update');
    Route::delete('customers/{customer}', [ClienteController::class, 'destroy'])->middleware('permission:customers.delete')->name('customers.destroy');
    Route::get('customers/export-pdf', [ClienteController::class, 'exportPdf'])->middleware('permission:customers.export')->name('customers.export-pdf');

    Route::get('proveedor', [ProveedorController::class, 'index'])->middleware('permission:suppliers.manage')->name('proveedor.index');
    Route::post('proveedor', [ProveedorController::class, 'store'])->middleware('permission:suppliers.manage')->name('proveedor.store');
    Route::put('proveedor/{proveedor}', [ProveedorController::class, 'update'])->middleware('permission:suppliers.manage')->name('proveedor.update');
    Route::delete('proveedor/{proveedor}', [ProveedorController::class, 'destroy'])->middleware('permission:suppliers.manage')->name('proveedor.destroy');
    Route::get('proveedor/export-pdf', [ProveedorController::class, 'exportPdf'])->middleware('permission:suppliers.manage')->name('proveedor.export-pdf');

    Route::get('compra', [CompraController::class, 'index'])->middleware('permission:purchases.manage')->name('compra.index');
    Route::post('compra', [CompraController::class, 'store'])->middleware('permission:purchases.manage')->name('compra.store');
    Route::put('compra/{compra}', [CompraController::class, 'update'])->middleware('permission:purchases.manage')->name('compra.update');
    Route::delete('compra/{compra}', [CompraController::class, 'destroy'])->middleware('permission:purchases.manage')->name('compra.destroy');
    Route::get('compra/export-pdf', [CompraController::class, 'exportPdf'])->middleware('permission:purchases.manage')->name('compra.export-pdf');

    Route::get('detalle', [DetalleCompraController::class, 'index'])->middleware('permission:purchases.manage')->name('detalle.index');
    Route::post('detalle', [DetalleCompraController::class, 'store'])->middleware('permission:purchases.manage')->name('detalle.store');
    Route::put('detalle/{detalle}', [DetalleCompraController::class, 'update'])->middleware('permission:purchases.manage')->name('detalle.update');
    Route::delete('detalle/{detalle}', [DetalleCompraController::class, 'destroy'])->middleware('permission:purchases.manage')->name('detalle.destroy');
    Route::get('detalles/export-pdf', [DetalleCompraController::class, 'exportPdf'])->middleware('permission:purchases.manage')->name('detalles.export-pdf');

    Route::get('mesas', [MesaController::class, 'index'])->middleware('permission:tables.view')->name('mesas.index');
    Route::post('mesas', [MesaController::class, 'store'])->middleware('permission:tables.manage')->name('mesas.store');
    Route::put('mesas/{mesa}', [MesaController::class, 'update'])->middleware('permission:tables.update')->name('mesas.update');
    Route::delete('mesas/{mesa}', [MesaController::class, 'destroy'])->middleware('permission:tables.manage')->name('mesas.destroy');

    Route::get('menu', [MenuProductController::class, 'index'])->middleware('permission:menu.manage')->name('menu.index');
    Route::post('menu', [MenuProductController::class, 'store'])->middleware('permission:menu.manage')->name('menu.store');
    Route::put('menu/{product}', [MenuProductController::class, 'update'])->middleware('permission:menu.manage')->name('menu.update');
    Route::delete('menu/{product}', [MenuProductController::class, 'destroy'])->middleware('permission:menu.manage')->name('menu.destroy');

    Route::get('orden', [OrdenController::class, 'index'])->middleware('permission:orders.view')->name('orden.index');
    Route::post('orden', [OrdenController::class, 'store'])->middleware('permission:orders.create')->name('orden.store');
    Route::put('orden/{orden}', [OrdenController::class, 'update'])->middleware('permission:orders.update')->name('orden.update');
    Route::delete('orden/{orden}', [OrdenController::class, 'destroy'])->middleware('permission:orders.delete')->name('orden.destroy');
    Route::middleware('admin')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';