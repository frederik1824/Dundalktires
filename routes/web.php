<?php

use App\Http\Controllers\Appointments\AppointmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Landing\PromotionController;
use App\Http\Controllers\Landing\TestimonialController;
use App\Http\Controllers\Landing\WebLeadController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Customers\CustomerController;
use App\Http\Controllers\Customers\VehicleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employees\EmployeeController;
use App\Http\Controllers\Expenses\ExpenseController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\SupplierController;
use App\Http\Controllers\OperationalCosts\TireDisposalController;
use App\Http\Controllers\OperationalCosts\WorkOrderConsumableController;
use App\Http\Controllers\POS\CreditNoteController;
use App\Http\Controllers\POS\InvoiceController;
use App\Http\Controllers\Purchases\PurchaseOrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reports\CashCloseController;
use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\Services\ServiceController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\WorkOrders\WorkOrderController;
use Illuminate\Support\Facades\Route;

// Public landing
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/quote', [LandingController::class, 'quote'])->name('landing.quote');

// Auth
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// App
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', fn () => redirect()->route('dashboard'));

    // Customers
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::resource('customers', CustomerController::class);

    // Vehicles (nested under customer)
    Route::resource('customers.vehicles', VehicleController::class)
        ->only(['show', 'create', 'store', 'edit', 'update', 'destroy']);

    // Inventory
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::post('/products/{product}/adjust', [ProductController::class, 'adjust'])->name('products.adjust');
    Route::post('/products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class)->except(['show']);

    // Purchases
    Route::post('/purchases/{purchase}/receive',    [PurchaseOrderController::class, 'receive'])->name('purchases.receive');
    Route::post('/purchases/{purchase}/attachment', [PurchaseOrderController::class, 'uploadAttachment'])->name('purchases.attachment');
    Route::patch('/purchases/{purchase}/status',    [PurchaseOrderController::class, 'updateStatus'])->name('purchases.status');
    Route::resource('purchases', PurchaseOrderController::class)->except(['edit', 'update']);

    // Services
    Route::get('/services/search', [ServiceController::class, 'search'])->name('services.search');
    Route::resource('services', ServiceController::class)->except(['show']);

    // Work Orders
    Route::patch('/work-orders/{workOrder}/status', [WorkOrderController::class, 'updateStatus'])->name('work-orders.status');
    Route::resource('work-orders', WorkOrderController::class);

    // Operational Costs — Consumables per Work Order
    Route::post('/work-orders/{workOrder}/consumables', [WorkOrderConsumableController::class, 'store'])->name('work-orders.consumables.store');
    Route::delete('/work-orders/{workOrder}/consumables/{consumable}', [WorkOrderConsumableController::class, 'destroy'])->name('work-orders.consumables.destroy');

    // Operational Costs — Tire Disposals per Work Order
    Route::post('/work-orders/{workOrder}/tire-disposal', [TireDisposalController::class, 'store'])->name('work-orders.tire-disposal.store');
    Route::delete('/work-orders/{workOrder}/tire-disposal/{disposal}', [TireDisposalController::class, 'destroy'])->name('work-orders.tire-disposal.destroy');

    // POS / Invoices
    Route::post('/invoices/{invoice}/payment',     [InvoiceController::class, 'addPayment'])->name('invoices.payment');
    Route::patch('/invoices/{invoice}/void',       [InvoiceController::class, 'void'])->name('invoices.void');
    Route::get('/invoices/{invoice}/pdf',          [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::get('/invoices/{invoice}/receipt',      [InvoiceController::class, 'receipt'])->name('invoices.receipt');
    Route::get('/invoices/{invoice}/credit-note',  [CreditNoteController::class, 'create'])->name('invoices.credit-note.create');
    Route::post('/invoices/{invoice}/credit-note', [CreditNoteController::class, 'store'])->name('invoices.credit-note.store');
    Route::resource('invoices', InvoiceController::class)->except(['edit', 'update']);
    // Alias /pos → invoice create
    Route::get('/pos', [InvoiceController::class, 'create'])->name('pos');

    // Expenses
    Route::resource('expenses', ExpenseController::class)->except(['show']);

    // Reports
    Route::get('/reports',             [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/ar',          [ReportController::class, 'arAging'])->name('reports.ar');
    Route::get('/reports/cash-close',         [ReportController::class, 'cashClose'])->name('reports.cash-close');
    Route::post('/reports/cash-close/open',   [CashCloseController::class, 'open'])->name('reports.cash-close.open');
    Route::post('/reports/cash-close',        [CashCloseController::class, 'store'])->name('reports.cash-close.store');
    Route::post('/reports/cash-close/reopen', [CashCloseController::class, 'reopen'])->name('reports.cash-close.reopen');
    Route::get('/reports/export/csv',  [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('/reports/export/pdf',  [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Appointments
    Route::resource('appointments', AppointmentController::class)->except(['show']);

    // Landing — Web Leads
    Route::resource('web-leads', WebLeadController::class)->only(['index', 'update', 'destroy']);

    // Landing — Promotions
    Route::resource('landing/promotions', PromotionController::class)
        ->except(['show'])
        ->names('landing.promotions');

    // Landing — Testimonials
    Route::resource('landing/testimonials', TestimonialController::class)
        ->except(['show'])
        ->names('landing.testimonials');

    // Employees
    Route::post('/employees/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])->name('employees.reset-password');
    Route::resource('employees', EmployeeController::class)->except(['show']);

    // Profile
    Route::get('/profile',          [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
});
