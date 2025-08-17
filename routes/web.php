<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POSController;

// Root redirect to admin
Route::get('/', function () {
    return redirect('/admin');
});

// Health check route
Route::get('/health', function () {
    return response('OK', 200)->header('Content-Type', 'text/plain');
});

// Test routes
Route::get('/hello', function () {
    return 'Hello World! This route works!';
});

Route::get('/test', function () {
    return 'Test route is working!';
});

Route::get('/simple', function () {
    return 'Hello World!';
});

Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return 'Database connection successful!';
    } catch (Exception $e) {
        return 'Database connection failed: ' . $e->getMessage();
    }
});

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

// POS Route - requires authentication
Route::get('/pos', function () {
    // Check if user is authenticated
    if (!auth()->check()) {
        // Redirect to Filament login if not authenticated
        return redirect('/admin/login');
    }
    
    try {
        return view('pos.standalone');
    } catch (Exception $e) {
        return 'Error loading POS: ' . $e->getMessage();
    }
})->name('pos.standalone');

// Simple POS test without Livewire
Route::get('/pos-simple', function () {
    return '<h1>POS Simple Test</h1><p>This works without Livewire</p>';
});

// PDF Invoice Routes
Route::get('/invoice/{invoice}/pdf', [App\Http\Controllers\InvoiceController::class, 'generatePdf'])->name('invoice.pdf');
Route::get('/invoice/{invoice}/view-pdf', [App\Http\Controllers\InvoiceController::class, 'viewPdf'])->name('invoice.view-pdf');
