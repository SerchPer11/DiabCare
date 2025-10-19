
<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/home');
Route::get('/home', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Ruta temporal para probar colores
Route::get('/test-colors', function () {
    return Inertia::render('TestColors');
})->name('test.colors');

Route::middleware('auth')->group(function () {
    //Administración de pacientes
    Route::resource('security/modules', ModuleController::class)->names('modules');
    Route::resource('security/permissions', PermissionController::class)->names('permissions');
    Route::resource('security/roles', RoleController::class)->names('roles');
    Route::resource('administration/users', UserController::class)->names('users');

    
});

require __DIR__.'/auth.php';
