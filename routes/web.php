
<?php

use App\Http\Controllers\Doctor\Catalogs\MedicationController;
use App\Http\Controllers\Doctor\Catalogs\ExerciseController;
use App\Http\Controllers\Patient\MedicalHistoryController;
use App\Http\Controllers\Patient\PatientProfileController;
use App\Http\Controllers\Doctor\DoctorProfileController;
use App\Http\Controllers\Doctor\Catalogs\FoodController;
use App\Http\Controllers\Doctor\RecomendationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\FileController;
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
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'ensure.profile.complete'])->name('dashboard');

// Ruta temporal para probar colores
Route::get('/test-colors', function () {
    return Inertia::render('TestColors');
})->name('test.colors');

Route::get('file/serve/{file}', [FileController::class, 'serveFile'])->name('file.serve')->middleware('signed');

Route::middleware([ 'auth', 'ensure.profile.complete'])->group(function () {
    //Administración de pacientes
    Route::resource('security/modules', ModuleController::class)->names('modules');
    Route::resource('security/permissions', PermissionController::class)->names('permissions');
    Route::resource('security/roles', RoleController::class)->names('roles');
    Route::resource('administration/users', UserController::class)->names('users');

    Route::prefix('catalogs')->name('catalogs.')->group(function () {
        
    });

    Route::prefix('doctor')->name('doctor.')->group(function () {
        //Rutas para gestión de perfil de doctor
        Route::get('/profile', [DoctorProfileController::class, 'profile'])->name('profile.show');
        Route::put('/profile', [DoctorProfileController::class, 'update'])->name('profile.update');
        //Rutas para gestión de doctores

        //rutas para gestión de catálogos
        Route::resource('catalogs/medications', MedicationController::class)->names('catalogs.medications');
        Route::resource('catalogs/exercises', ExerciseController::class)->names('catalogs.exercises');
        Route::resource('catalogs/foods', FoodController::class)->names('catalogs.foods');

        //Rutas para gestión de citas médicas
        Route::resource('appointments', AppointmentController::class)->names('appointments');
        Route::resource('recomendations', RecomendationController::class)->names('recomendations');
    });

    Route::prefix('patient')->name('patient.')->group(function () {
        //Rutas para gestión de perfil de paciente
        Route::get('/profile', [PatientProfileController::class, 'profile'])->name('profile.show');
        Route::put('/profile', [PatientProfileController::class, 'update'])->name('profile.update');

        //Rutas para gestión de pacientes
        Route::get('medical-history', [MedicalHistoryController::class, 'index'])->name('medical-history.index');
        Route::put('medical-history', [MedicalHistoryController::class, 'update'])->name('medical-history.update');
    });
    
});



require __DIR__.'/auth.php';
