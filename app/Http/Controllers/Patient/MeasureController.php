<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StoreMeasureRequest;
use Illuminate\Http\Request;
use App\Models\Patient\Measure;
use App\Traits\Filterable;
use App\Http\Resources\Patient\MeasureResource;
use App\Models\Patient\MeasureConfig;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Patient\MeasureType;
use Inertia\Inertia;
use App\Http\Requests\Patient\UpdateMeasureRequest;
use Illuminate\Support\Facades\Auth;

class MeasureController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'measures.';
        $this->source = 'Patient/Measures/Pages/';
        $this->model = new Measure();

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}edit")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
    }

    public function index(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->with(['measureConfig', 'measureConfig.patient', 'measureConfig.measureType'])
            ->when(!$user->hasRole('doctor'), function ($query) use ($user) {
                $query->whereHas('measureConfig', function ($q) use ($user) {
                    $q->where('patient_id', $user->id);
                });
            })
            ->when($filters->search, function ($query, $search) {
                $query->where('value', 'LIKE', '%' . $search . '%')
                    ->orWhere('measured_at', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('measureConfig.patient', function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('measureConfig.measureType', function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('unit', 'LIKE', '%' . $search . '%');
                    });
            });

        $measures = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'measures' => MeasureResource::collection($measures),
            'title'           => 'Mediciones',
            'routeName'       => $this->routeName,
            'filters'         => $filters
        ]);
    }

    public function create(Request $request)
    {
        $config = null;
        $patient_id = $request->input('patient_id');
        $measure_type_id = $request->input('measure_type_id');

        $frequencies = [
            ['value' => 'daily', 'label' => 'Diario'],
            ['value' => 'weekly', 'label' => 'Semanal'],
            ['value' => 'monthly', 'label' => 'Mensual'],
            ['value' => 'yearly', 'label' => 'Anual'],
        ];

        $severities = [
            ['value' => 'low', 'label' => 'Baja'],
            ['value' => 'medium', 'label' => 'Media'],
            ['value' => 'high', 'label' => 'Alta'],
        ];

        $ranges = [
            ['value' => 'outrange', 'label' => 'Fuera de rango'],
            ['value' => 'above', 'label' => 'Por encima'],
            ['value' => 'below', 'label' => 'Por debajo'],
        ];

        if ($patient_id && $measure_type_id) {
            $config = MeasureConfig::where('patient_id', $patient_id)
                ->where('measure_type_id', $measure_type_id)
                ->first();
        }

        return Inertia::render("{$this->source}Create", [
            'title'       => 'Mediciónes',
            'routeName'   => $this->routeName,
            'patients'    => User::role('patient')->select(['id', 'name', 'last_name', DB::raw("CONCAT(name, ' ', last_name) as full_name")])->get(),
            'types'       => MeasureType::all(),
            'config'      => $config,
            'frequencies' => $frequencies,
            'severities'  => $severities,
            'ranges'      => $ranges,
        ]);
    }

    public function store(StoreMeasureRequest $request)
    {
        DB::Transaction(function () use ($request) {
            $validatedData = $request->validated();
            $config = MeasureConfig::where('patient_id', $validatedData['patient_id'])
                ->where('measure_type_id', $validatedData['measure_type_id'])
                ->first();

            $config = MeasureConfig::updateOrCreate(
                [
                    'patient_id' => $validatedData['patient_id'],
                    'measure_type_id' => $validatedData['measure_type_id'],
                ],
                [
                    'min_value' => $validatedData['min_value'],
                    'max_value' => $validatedData['max_value'],
                    'range' => $validatedData['range'],
                    'severity' => $validatedData['severity'],
                    'frequency' => $validatedData['frequency'],
                ]
            );

            Measure::create([
                'measure_config_id' => $config->id,
                'value' => $validatedData['value'],
                'measured_at' => $validatedData['measured_at'],
                'hour_measured' => $validatedData['hour_measured'],
                'notes' => $validatedData['notes'],
            ]);
        });
        return redirect()->route($this->routeName . 'index')
            ->with('success', 'Medición creada correctamente.');
    }

    public function edit(Measure $measure, Request $request)
    {
        $config = null;
        $patient_id = $request->input('patient_id');
        $measure_type_id = $request->input('measure_type_id');

        $frequencies = [
            ['value' => 'daily', 'label' => 'Diario'],
            ['value' => 'weekly', 'label' => 'Semanal'],
            ['value' => 'monthly', 'label' => 'Mensual'],
            ['value' => 'yearly', 'label' => 'Anual'],
        ];

        $severities = [
            ['value' => 'low', 'label' => 'Baja'],
            ['value' => 'medium', 'label' => 'Media'],
            ['value' => 'high', 'label' => 'Alta'],
        ];

        $ranges = [
            ['value' => 'outrange', 'label' => 'Fuera de rango'],
            ['value' => 'above', 'label' => 'Por encima'],
            ['value' => 'below', 'label' => 'Por debajo'],
        ];

        if ($patient_id && $measure_type_id) {
            $config = MeasureConfig::where('patient_id', $patient_id)
                ->where('measure_type_id', $measure_type_id)
                ->first();
        }

        $measure->load('measureConfig');

        return Inertia::render("{$this->source}Edit", [
            'title'       => 'Mediciónes',
            'routeName'   => $this->routeName,
            'patients'    => User::role('patient')->select(['id', 'name', 'last_name', DB::raw("CONCAT(name, ' ', last_name) as full_name")])->get(),
            'types'       => MeasureType::all(),
            'config'      => $config,
            'frequencies' => $frequencies,
            'severities'  => $severities,
            'ranges'      => $ranges,
            'measure'     => new MeasureResource($measure),
        ]);
    }

    public function update(UpdateMeasureRequest $request, Measure $measure)
    {
        DB::Transaction(function () use ($request, $measure) {
            $validatedData = $request->validated();
            $config = MeasureConfig::where('patient_id', $validatedData['patient_id'])
                ->where('measure_type_id', $validatedData['measure_type_id'])
                ->first();

            $config = MeasureConfig::updateOrCreate(
                [
                    'patient_id' => $validatedData['patient_id'],
                    'measure_type_id' => $validatedData['measure_type_id'],
                ],
                [
                    'min_value' => $validatedData['min_value'],
                    'max_value' => $validatedData['max_value'],
                    'range' => $validatedData['range'],
                    'severity' => $validatedData['severity'],
                    'frequency' => $validatedData['frequency'],
                ]
            );

            $measure->update([
                'measure_config_id' => $config->id,
                'value' => $validatedData['value'],
                'measured_at' => $validatedData['measured_at'],
                'hour_measured' => $validatedData['hour_measured'],
                'notes' => $validatedData['notes'],
            ]);
        });
        return redirect()->route($this->routeName . 'index')
            ->with('success', 'Medición actualizada correctamente.');
    }

    public function destroy(Measure $measure)
    {
        $measure->delete();

        return redirect()->route($this->routeName . 'index')
            ->with('success', 'Medición eliminada correctamente.');
    }
}
