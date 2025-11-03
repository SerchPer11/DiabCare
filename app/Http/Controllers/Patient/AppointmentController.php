<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'patient.appointments.';
        $this->source = 'Patient/Appointment/Pages/';
        $this->model = new Appointment();

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}update-status")->only(['updateStatus']);
    }

    /**
     * Display a listing of the patient's appointments.
     */
    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        
        // Solo mostrar las citas del paciente autenticado
        $query = $this->model->with(['patient', 'doctor', 'status'])
            ->where('patient_id', Auth::id())
            ->when($filters->search, function ($query, $search) {
                $query->where('reason', 'LIKE', '%' . $search . '%')
                    ->orWhere('modality', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('doctor', function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%')
                          ->orWhere('last_name', 'LIKE', '%' . $search . '%');
                    });
            });

        $appointments = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title'         => 'Mis Citas',
            'appointments'  => $appointments,
            'routeName'     => $this->routeName,
            'filters'       => $filters
        ]);
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        // Verificar que la cita pertenece al paciente autenticado
        if ($appointment->patient_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver esta cita.');
        }

        $appointment->load(['patient', 'doctor', 'status']);

        // Obtener estados disponibles para el paciente
        $availableStatuses = AppointmentStatus::whereIn('name', ['aceptada', 'cancelada'])->get();

        return Inertia::render("{$this->source}Show", [
            'title'            => 'Detalle de Cita',
            'appointment'      => $appointment,
            'routeName'        => $this->routeName,
            'availableStatuses' => $availableStatuses,
        ]);
    }

    /**
     * Update the status of an appointment.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Verificar que la cita pertenece al paciente autenticado
        if ($appointment->patient_id !== Auth::id()) {
            abort(403, 'No tienes permiso para modificar esta cita.');
        }

        $request->validate([
            'status_id' => 'required|exists:appointment_statuses,id',
        ]);

        // Solo permitir ciertos cambios de estado por parte del paciente
        $allowedStatuses = ['aceptada', 'cancelada'];
        $newStatus = AppointmentStatus::find($request->status_id);

        if (!$newStatus || !in_array($newStatus->name, $allowedStatuses)) {
            return back()->withErrors(['status' => 'No tienes permiso para cambiar a este estado.']);
        }

        // Verificar que el estado actual permita el cambio
        $currentStatus = $appointment->status->name;
        
        if (in_array($currentStatus, ['completada', 'no asistió'])) {
            return back()->withErrors(['status' => 'No se puede cambiar el estado de una cita completada o que no asistió.']);
        }

        // Actualizar el estado
        $appointment->update([
            'appointment_status_id' => $request->status_id,
        ]);

        // Recargar la cita con las relaciones actualizadas
        $appointment->refresh();
        $appointment->load(['patient', 'doctor', 'status']);

        // Enviar correo de notificación al doctor
        Mail::to($appointment->doctor->email)->send(new \App\Mail\AppointmentStatusChanged($appointment, $currentStatus, $newStatus->name));

        // Mensaje de éxito dependiendo del estado
        $message = $newStatus->name === 'aceptada' 
            ? 'Cita aceptada correctamente. Se ha notificado al doctor.' 
            : 'Cita cancelada correctamente. Se ha notificado al doctor.';

        return redirect()->route('patient.appointments.index')->with('success', $message);
    }
}