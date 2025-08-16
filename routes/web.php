<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POSController;

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

Route::get('/', function () {
    return redirect('/admin/login');
});

// Simple test route
Route::get('/hello', function () {
    return 'Hello World! This route works!';
});

// Health check route
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'app' => config('app.name'),
        'env' => config('app.env')
    ]);
});

// Simple test route
Route::get('/test', function () {
    return 'Test route is working!';
});

// Very simple route
Route::get('/simple', function () {
    return 'Hello World!';
});

// Database test route
Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return 'Database connection successful!';
    } catch (Exception $e) {
        return 'Database connection failed: ' . $e->getMessage();
    }
});

// Simple POS test route
Route::get('/pos-test', function () {
    return 'POS test route is working!';
});

// Route list for debugging
Route::get('/routes', function () {
    $routes = [];
    foreach (Route::getRoutes() as $route) {
        $routes[] = $route->getUri() . ' [' . implode('|', $route->methods()) . ']';
    }
    return response()->json($routes);
});

// Standalone POS Route (protected by auth middleware)
Route::get('/pos', function () {
    try {
        return view('pos.standalone');
    } catch (Exception $e) {
        return 'Error loading POS: ' . $e->getMessage();
    }
})->middleware(['auth'])->name('pos.standalone');

// Simple POS test without Livewire
Route::get('/pos-simple', function () {
    return '<h1>POS Simple Test</h1><p>This works without Livewire</p>';
});

// PDF Invoice Routes (no middleware for testing)
Route::get('/invoice/{invoice}/pdf', [App\Http\Controllers\InvoiceController::class, 'generatePdf'])->name('invoice.pdf');
Route::get('/invoice/{invoice}/view-pdf', [App\Http\Controllers\InvoiceController::class, 'viewPdf'])->name('invoice.view-pdf');
