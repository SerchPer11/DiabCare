<?php

namespace App\Http\Controllers;

use App\Services\Reports\DiabetesTypeReportService;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    use Filterable;
    protected $source;
    protected $routeName;

    public function __construct()
    {
        $this->source = 'Reports/Pages/';
        $this->routeName = 'reports.';

        /*$this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);*/
    }

    protected $reportServices = [
        'diabetes-type' => DiabetesTypeReportService::class,
        /*'user-activity' => UserActivityReportService::class,*/
    ];

    public function index()
    {
        return Inertia::render("{$this->source}Index", [
            'title'     => 'Reportes',
            'routeName' => $this->routeName,
        ]);
    }

    public function show(Request $request, $reportType)
    {
        if (!isset($this->reportServices[$reportType])) {
            abort(404, 'Reporte no encontrado');
        }

        $serviceClass = $this->reportServices[$reportType];
        $service = new $serviceClass();

        $reportData = $service->generate($request->all());

        return Inertia::render("{$this->source}Show", [
            'title'     => 'Reportes',
            'reportTitle' => $reportData['reportTitle'] ?? 'Reporte',
            'chartData'   => $reportData['chartData'] ?? [],
            'tableData'   => $reportData['tableData'] ?? [],
            'stats'       => $reportData['stats'] ?? [],
            'filters'     => $reportData['filters'] ?? [],
            'routeName' => $this->routeName,
            'report' => $reportData,
            'reportType' => $reportData['reportType'],
            'reportTitle' => $reportData['reportTitle']
        ]);
    }

    public function export(Request $request, $reportType)
    {
        if (!isset($this->reportServices[$reportType])) {
            abort(404, 'Reporte no encontrado');
        }

        // 1. Obtenemos los datos de la TABLA (la gráfica ya viene como imagen)
        $serviceClass = $this->reportServices[$reportType];
        $service = new $serviceClass();
        $reportData = $service->generate($request->all());

        // 2. Recibimos la imagen de la gráfica desde Vue
        $chartImage = $request->input('chartImage'); // El string Base64

        // 3. Preparamos los datos para la plantilla Blade
        $dataForPdf = [
            // Usamos la llave correcta del servicio
            'title' => $reportData['reportTitle'] ?? 'Reporte',
            'tableData' => $reportData['tableData'] ?? [],
            'chartImage' => $chartImage,
            'stats' => $reportData['stats'] ?? [],
        ];

        // 4. Cargamos la PLANTILLA REUTILIZABLE de PDF
        $pdf = Pdf::loadView('pdf_template', $dataForPdf);

        return $pdf->download("reporte-{$reportType}.pdf");
    }
}
