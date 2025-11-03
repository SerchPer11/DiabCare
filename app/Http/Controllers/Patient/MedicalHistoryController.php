<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\UpdatePatientPathologiesRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Traits\Filterable;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\Patient\PatientPathologies;
use Inertia\Response;
use App\Http\Resources\Patient\PatientPathologiesResource;

class MedicalHistoryController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;
    public function __construct()
    {
        $this->routeName = 'patient.medical-history.';
        $this->source = 'Patient/MedicalHistory/Pages/';
        //$this->model = new Exercise();

        $this->middleware("permission:{$this->routeName}index")->only(['index']);
        $this->middleware("permission:{$this->routeName}edit")->only(['update']);
    }

    public function index(): Response
    {
        $history = Auth::user()->profileable->pathology;
        $user = User::with(['profileable.pathology'])->find(Auth::user()->id);
        return Inertia::render("{$this->source}Index", [
            'title'         => 'Historial Médico',
            'routeName'     => $this->routeName,
            'user'         => new UserResource($user),
            'history' => $history ? new PatientPathologiesResource($history) : null,
        ]);
    }

    public function update(UpdatePatientPathologiesRequest $request)
    {
        $validatedData = $request->validated();

        DB::Transaction(function () use ($validatedData, $request) {
            $patientProfile = Auth::user()->profileable;

            // 2. Usamos updateOrCreate para manejar la lógica de forma atómica y limpia.
            PatientPathologies::updateOrCreate(
                [
                    // Laravel buscará un registro que coincida con este criterio...
                    'patient_profile_id' => $patientProfile->id,
                ],
                [
                    // ...y si lo encuentra, lo actualizará con estos datos.
                    // Si no lo encuentra, creará un nuevo registro con todos estos datos.
                    'diabetes' => $validatedData['diabetes'],
                    'diabetes_type' => $validatedData['diabetes_type'],
                    'diabetes_diagnosis_date' => $validatedData['diabetes_diagnosis_date'],
                    'hypertension' => $validatedData['hypertension'],
                    'hypertension_type' => $validatedData['hypertension_type'],
                    'hypertension_diagnosis_date' => $validatedData['hypertension_diagnosis_date'],
                    'obesity' => $validatedData['obesity'],
                    'obesity_type' => $validatedData['obesity_type'],
                    'allergies' => $validatedData['allergies'],
                    'allergies_details' => $validatedData['allergies_details'],
                ]
            );
        });

        return redirect()->route('patient.medical-history.index')
            ->with('success', 'Historial médico actualizado correctamente.');
    }
}
