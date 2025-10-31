<?php

namespace App\Http\Controllers\Patient;

use App\Http\Resources\Patient\ClinicalLogResource;
use App\Http\Requests\UpdateClinicalLogRequest;
use App\Http\Requests\StoreClinicalLogRequest;
use App\Models\Patient\ClinicalLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Filterable;
use App\Models\User;
use Inertia\Inertia;

use Illuminate\Support\Facades\Auth;
class ClinicalLogController extends Controller
{
    use Filterable;
    protected $source;
    protected $routeName;
    public function __construct()
    {
        $this->source = 'Patient/ActivityLog/Pages/';
        $this->routeName = 'clinical-log.';

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     */
    public function show(User $patient)
    {
        $logsQuery = ClinicalLog::where('patient_id', $patient->id)
            ->with(['doctor', 'loggable'])
            ->orderBy('created_at', 'desc');
        $logs = $logsQuery->get();

        $user = User::where('id', Auth::user()->id)->first();
        if($user->hasRole('patient')){
            $title = 'Mi Seguimiento Clínico';
        }

        return Inertia::render("{$this->source}Show", [
            'title'     => $title ?? 'Seguimiento Clínico de ' . $patient->name,
            'patient'   => $patient,
            'clinicalLogs'      => ClinicalLogResource::collection($logs),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClinicalLogRequest $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClinicalLog $clinicalLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClinicalLogRequest $request, ClinicalLog $clinicalLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClinicalLog $clinicalLog)
    {
        //
    }
}
