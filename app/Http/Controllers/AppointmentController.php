<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\User;
use App\Models\AppointmentStatus;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\LogClinicalActivity;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    use Filterable;
    use LogClinicalActivity;

    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'doctor.appointments.';
        $this->source = 'Doctor/Appointment/Pages/';
        $this->model = new Appointment();

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}edit")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->with(['patient', 'doctor', 'status'])
            ->when($filters->search, function ($query, $search) {
                $query->where('reason', 'LIKE', '%' . $search . '%')
                    ->orWhere('modality', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('patient', function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('doctor', function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%');
                    });
            });

        $appointments = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title'         => 'Citas',
            'appointments'  => $appointments,
            'routeName'     => $this->routeName,
            'filters'       => $filters
        ]);
    }

    public function create(Appointment $appointment)
    {
        $statusList = AppointmentStatus::all();
        $doctors = User::role('doctor')
            ->select(['id', 'name', 'last_name', 'email', DB::raw("CONCAT(name, ' ', last_name) as full_name")])
            ->get();
        $patients = User::role('patient')
            ->select(['id', 'name', 'last_name', 'email', DB::raw("CONCAT(name, ' ', last_name) as full_name")])
            ->get();
        
        $modalityOptions = [
            ['value' => 'Presencial', 'label' => 'Presencial'],
            ['value' => 'Virtual', 'label' => 'Virtual']
        ];
        
        return Inertia::render("{$this->source}Create", [
            'title'           => 'Citas',
            'routeName'       => $this->routeName,
            'statusList'      => $statusList,
            'doctors'         => $doctors,
            'patients'        => $patients,
            'modalityOptions' => $modalityOptions,
        ]);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $appointment = $this->model->create($request->validated());
        $this->logActivity(
            $appointment,
            'Cita medica',
            $appointment->patient_id,
            $appointment->doctor_id
        );
        return redirect()->route($this->routeName . 'index')
            ->with('success', 'Cita creada correctamente.');
    }

    public function edit(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'status']);
        $statusList = AppointmentStatus::all();
        $doctors = User::role('doctor')
            ->select(['id', 'name', 'last_name', 'email', DB::raw("CONCAT(name, ' ', last_name) as full_name")])
            ->get();
        $patients = User::role('patient')
            ->select(['id', 'name', 'last_name', 'email', DB::raw("CONCAT(name, ' ', last_name) as full_name")])
            ->get();
        
        $modalityOptions = [
            ['value' => 'Presencial', 'label' => 'Presencial'],
            ['value' => 'Virtual', 'label' => 'Virtual']
        ];
        
        return Inertia::render("{$this->source}Edit", [
            'title'           => 'Citas',
            'routeName'       => $this->routeName,
            'appointment'     => $appointment,
            'statusList'      => $statusList,
            'doctors'         => $doctors,
            'patients'        => $patients,
            'modalityOptions' => $modalityOptions,
        ]);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $appointment->update($request->validated());
        return redirect()->route($this->routeName . 'index')->with('success', 'Cita modificada con éxito');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route($this->routeName . 'index')->with('success', 'Cita eliminada con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        // Puedes implementar si necesitas mostrar detalles de una cita
    }

    /**
     * Show the form for editing the specified resource.
     */
    }


