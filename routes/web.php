
<?php

use App\Http\Controllers\Doctor\Catalogs\MedicationController;
use App\Http\Controllers\Doctor\Catalogs\ExerciseController;
use App\Http\Controllers\Patient\MedicalHistoryController;
use App\Http\Controllers\Patient\PatientProfileController;
use App\Http\Controllers\Doctor\DoctorProfileController;
use App\Http\Controllers\Doctor\Catalogs\FoodController;
use App\Http\Controllers\Doctor\RecomendationController;
use App\Http\Controllers\Patient\ClinicalLogController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Patient\MeasureController;
use App\Http\Controllers\Doctor\PatientsController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\FileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Doctor\DoctorSurveyController;
use App\Http\Controllers\Patient\PatientSurveyController;
use Inertia\Inertia;
use App\Models\User;



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
    $user = User::where('id', Auth::id())->first();
    $data = [];
    
    // Si es doctor, cargar estadísticas de encuestas
    if ($user->hasRole('doctor')) {
        $surveys = \App\Models\Survey::where('created_by', $user->id);
        $data['stats'] = [
            'total' => $surveys->count(),
            'active' => $surveys->where('is_active', true)->count(),
            'inactive' => $surveys->where('is_active', false)->count(),
            'total_responses' => \App\Models\SurveyResponse::whereIn('survey_id', 
                $surveys->pluck('id'))->count(),
            'average_response_rate' => 0, // Calcular después si es necesario
        ];
    }
    
    return Inertia::render('Dashboard', $data);
})->middleware(['auth', 'verified', 'ensure.profile.complete', 'ensure.medical.history.complete'])->name('dashboard');

// Ruta temporal para probar colores
Route::get('/test-colors', function () {
    return Inertia::render('TestColors');
})->name('test.colors');

Route::get('file/serve/{file}', [FileController::class, 'serveFile'])->name('file.serve')->middleware('signed');

Route::middleware([ 'auth', 'ensure.profile.complete', 'ensure.medical.history.complete'])->group(function () {
    
    // Doctor Survey routes
    Route::prefix('doctor')->name('doctor.')->group(function () {
        Route::resource('surveys', DoctorSurveyController::class);
        Route::patch('surveys/{survey}/toggle-status', [DoctorSurveyController::class, 'toggleStatus'])->name('surveys.toggle-status');
        Route::get('surveys/{survey}/results', [DoctorSurveyController::class, 'showResults'])->name('surveys.show-results');
        Route::get('survey-results', [DoctorSurveyController::class, 'results'])->name('surveys.results');
    });

    // Patient Survey routes
    Route::prefix('patient')->name('patient.')->group(function () {
        Route::get('surveys', [PatientSurveyController::class, 'index'])->name('surveys.index');
        Route::get('surveys/{survey}/answer', [PatientSurveyController::class, 'show'])->name('surveys.answer');
        Route::post('surveys/{survey}/save-progress', [PatientSurveyController::class, 'saveProgress'])->name('surveys.save-progress');
        Route::post('surveys/{survey}/submit', [PatientSurveyController::class, 'submitResponse'])->name('surveys.submit');
        Route::get('my-survey-responses', [PatientSurveyController::class, 'myResponses'])->name('surveys.my-responses');
        Route::get('survey-response/{response}', [PatientSurveyController::class, 'showResponse'])->name('surveys.show-response');
    });
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
    //vista de pacientes
        Route::resource('patients', PatientsController::class)->names('patients');

    Route::prefix('patient')->name('patient.')->group(function () {
        //Rutas para gestión de perfil de paciente
        Route::get('/profile', [PatientProfileController::class, 'profile'])->name('profile.show');
        Route::put('/profile', [PatientProfileController::class, 'update'])->name('profile.update');

        //Rutas para gestión de pacientes
        Route::get('medical-history', [MedicalHistoryController::class, 'index'])->name('medical-history.index');
        Route::put('medical-history', [MedicalHistoryController::class, 'update'])->name('medical-history.update');
        Route::get('clinical-log/{patient}', [ClinicalLogController::class, 'show'])->name('clinical-log.show');
    });

    Route::resource('measures', MeasureController::class)->names('measures');
    
});



require __DIR__.'/auth.php';
