
<?php

use App\Http\Controllers\Doctor\Catalogs\MedicationController;
use App\Http\Controllers\Doctor\Catalogs\ExerciseController;
use App\Http\Controllers\Doctor\DoctorProfileController;
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

Route::middleware([ 'auth', 'ensure.profile.complete'])->group(function () {
    //Administración de pacientes
    Route::resource('security/modules', ModuleController::class)->names('modules');
    Route::resource('security/permissions', PermissionController::class)->names('permissions');
    Route::resource('security/roles', RoleController::class)->names('roles');
    Route::resource('administration/users', UserController::class)->names('users');

    Route::prefix('catalogs')->name('catalogs.')->group(function () {
        //Rutas para gestión de usuarios
        
    });

    Route::prefix('doctor')->name('doctor.')->group(function () {
        //Rutas para gestión de perfil de doctor
        Route::get('/profile', [DoctorProfileController::class, 'profile'])->name('doctor.profile.show');
        Route::put('/profile', [DoctorProfileController::class, 'update'])->name('doctor.profile.update');
        //Rutas para gestión de doctores
        Route::resource('catalogs/medications', MedicationController::class)->names('catalogs.medications');
        Route::resource('catalogs/exercises', ExerciseController::class)->names('catalogs.exercises');
    });

    
});



require __DIR__.'/auth.php';
