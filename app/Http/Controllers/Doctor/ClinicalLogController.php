<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Doctor\ClinicalLogEntry;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Plan;
use App\Models\Doctor\Catalogs\Medication;
use App\Http\Requests\Doctor\StoreClinicalLogEntryRequest;
use App\Http\Requests\Doctor\UpdateClinicalLogEntryRequest;
use App\Http\Resources\Doctor\ClinicalLogEntryResource;
use App\Traits\Filterable;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Services\PhotoService;
use App\DTOs\PhotoStorageConfig;
use App\Services\FileService;
use App\DTOs\FileStorageConfig;
use Illuminate\Support\Facades\Log;

class ClinicalLogController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;
    protected PhotoStorageConfig $configPhotos;
    protected PhotoService $photoService;
    protected FileService $fileService;
    protected FileStorageConfig $configFiles;

    public function __construct(PhotoService $photoService, FileService $fileService)
    {
        $this->routeName = 'doctor.clinical-logbook.';
        $this->source = 'Doctor/ClinicalLogbook/Pages/';
        $this->model = new ClinicalLogEntry();
        $this->configPhotos = new PhotoStorageConfig('public', 'clinical-logbook/photos', 'photos');
        $this->photoService = $photoService;
        $this->fileService = $fileService;
        $this->configFiles = new FileStorageConfig('private', 'clinical-logbook/files', 'files');

        $this->middleware("permission:clinical-logbook.index")->only(['index']);
        $this->middleware("permission:clinical-logbook.create")->only(['create', 'store']);
        $this->middleware("permission:clinical-logbook.edit")->only(['edit', 'update']);
        $this->middleware("permission:clinical-logbook.delete")->only(['destroy']);
    }

    /**
     * Display a listing of clinical log entries with filters
     */
    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        
        // Filtros adicionales específicos
        $patientId = $request->get('patient_id');
        $eventType = $request->get('event_type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = ClinicalLogEntry::with(['patient:id,name,last_name,email', 'doctor:id,name,last_name', 'related'])
            ->active()
            ->when($filters->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', '%' . $search . '%')
                      ->orWhere('description', 'LIKE', '%' . $search . '%')
                      ->orWhereHas('patient', function ($q) use ($search) {
                          $q->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $search . '%');
                      });
                });
            })
            ->when($patientId, function ($query, $patientId) {
                $query->byPatient($patientId);
            })
            ->when($eventType, function ($query, $eventType) {
                $query->byEventType($eventType);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->dateRange($startDate, $endDate);
            });

        // Si el usuario es doctor, solo mostrar sus registros
        // Los admin pueden ver todos los registros
        if (Auth::user()->role === 'doctor') {
            $query->byDoctor(Auth::id());
        }

        $entries = $query->orderBy('event_datetime', 'desc')
            ->paginate($filters->rows)
            ->withQueryString();

        // Obtener listas para filtros
        $patients = User::role('patient')
            ->select(['id', 'name', 'last_name', DB::raw("CONCAT(name, ' ', last_name) as full_name")])
            ->get()
            ->map(fn($patient) => [
                'value' => $patient->id,
                'label' => $patient->full_name
            ]);

        $eventTypes = collect(ClinicalLogEntry::EVENT_TYPES)
            ->map(fn($label, $value) => [
                'value' => $value,
                'label' => $label
            ])->values();

        return Inertia::render("{$this->source}Index", [
            'title' => 'Bitácora Clínica',
            'entries' => ClinicalLogEntryResource::collection($entries),
            'routeName' => $this->routeName,
            'filters' => array_merge((array) $filters, [
                'patient_id' => $patientId,
                'event_type' => $eventType,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]),
            'patients' => $patients,
            'eventTypes' => $eventTypes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title' => 'Nueva Entrada de Bitácora',
            'routeName' => $this->routeName,
            'patients' => $this->getPatientsList(),
            'eventTypes' => $this->getEventTypesList(),
            'appointments' => $this->getAppointmentsList(),
            'plans' => $this->getPlansList(),
            'medications' => $this->getMedicationsList(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClinicalLogEntryRequest $request)
    {
        $validated = $request->validated();
        $validated['doctor_id'] = Auth::id();
        $validated['event_datetime'] = now();
        $validated['is_active'] = true;

        DB::transaction(function () use ($validated, $request) {
            $entry = ClinicalLogEntry::create($validated);

            // Procesar fotos igual que en recomendaciones
            $this->photoService->storePhotos($entry, $validated['photos'] ?? [], $this->configPhotos);
            
            // Procesar archivos igual que en recomendaciones
            $this->fileService->storeFiles($entry, $validated['files'] ?? [], $this->configFiles);
        });

        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Entrada de bitácora creada exitosamente');
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClinicalLogEntry $clinicalLogbook)
    {
        $clinicalLogbook->load(['patient', 'doctor', 'related', 'photos', 'files']);

        return Inertia::render("{$this->source}Edit", [
            'title' => 'Editar Entrada de Bitácora',
            'entry' => new ClinicalLogEntryResource($clinicalLogbook),
            'patients' => $this->getPatientsList(),
            'eventTypes' => $this->getEventTypesList(),
            'appointments' => $this->getAppointmentsList(),
            'plans' => $this->getPlansList(),
            'medications' => $this->getMedicationsList(),
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClinicalLogEntryRequest $request, ClinicalLogEntry $clinicalLogbook)
    {
        $validated = $request->validated();
        $validated['doctor_id'] = Auth::id();
        $validated['event_datetime'] = now();

        DB::transaction(function () use ($validated, $request, $clinicalLogbook) {
            $clinicalLogbook->update($validated);

            // Sincronizar fotos igual que en recomendaciones
            $this->photoService->syncPhotos($clinicalLogbook, $validated['photos'] ?? [], $this->configPhotos);
            
            // Sincronizar archivos igual que en recomendaciones
            $this->fileService->syncFiles($clinicalLogbook, $validated['files'] ?? [], $this->configFiles);
        });

        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Entrada de bitácora actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClinicalLogEntry $clinicalLogbook)
    {
        DB::transaction(function () use ($clinicalLogbook) {
            // Eliminar fotos y archivos asociados
            $this->photoService->deletePhotos($clinicalLogbook->photos, $this->configPhotos->disk, true);
            $this->fileService->deleteFiles($clinicalLogbook->files, $this->configFiles->disk, true);
            
            // Desactivar entrada (soft delete)
            $clinicalLogbook->update(['is_active' => false]);
        });

        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Entrada de bitácora desactivada exitosamente');
    }

    // Helper methods
    private function getPatientsList()
    {
        return User::role('patient')
            ->select(['id', 'name', 'last_name', DB::raw("CONCAT(name, ' ', last_name) as full_name")])
            ->get()
            ->map(fn($patient) => [
                'value' => $patient->id,
                'label' => $patient->full_name
            ]);
    }

    private function getEventTypesList()
    {
        return collect(ClinicalLogEntry::EVENT_TYPES)
            ->map(fn($label, $value) => [
                'value' => $value,
                'label' => $label
            ])->values();
    }

    private function getAppointmentsList()
    {
        return Appointment::with(['patient', 'status'])
            ->where('doctor_id', Auth::id())
            ->orderBy('date', 'desc')
            ->limit(50)
            ->get()
            ->map(fn($appointment) => [
                'value' => $appointment->id,
                'label' => "Cita: {$appointment->patient->name} {$appointment->patient->last_name} - " . 
                          Carbon::parse($appointment->date)->format('d/m/Y'),
                'type' => 'App\\Models\\Appointment'
            ]);
    }

    private function getPlansList()
    {
        return Plan::with('patient')
            ->where('assigned_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(fn($plan) => [
                'value' => $plan->id,
                'label' => "Plan: {$plan->title} - {$plan->patient->name} {$plan->patient->last_name}",
                'type' => 'App\\Models\\Plan'
            ]);
    }

    private function getMedicationsList()
    {
        return Medication::where('is_active', true)
            ->orderBy('name')
            ->limit(100)
            ->get()
            ->map(fn($medication) => [
                'value' => $medication->id,
                'label' => "Medicamento: {$medication->name}",
                'type' => 'App\\Models\\Doctor\\Catalogs\\Medication'
            ]);
    }

    private function getRelatedName($entry)
    {
        if (!$entry->related) return null;

        switch ($entry->related_type) {
            case 'App\\Models\\Appointment':
                return "Cita: " . $entry->related->patient->name . " - " . 
                       Carbon::parse($entry->related->date)->format('d/m/Y');
            case 'App\\Models\\Plan':
                return "Plan: " . $entry->related->title;
            case 'App\\Models\\Doctor\\Catalogs\\Medication':
                return "Medicamento: " . $entry->related->name;
            default:
                return 'Elemento relacionado';
        }
    }


}
