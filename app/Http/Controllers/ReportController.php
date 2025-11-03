<?php

namespace App\Http\Controllers;

use App\Services\Reports\AppointmentStatusReportService;
use App\Services\Reports\DiabetesTypeReportService;
use App\Services\Reports\MeasuresReportService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Filterable;
use Inertia\Inertia;

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
        'measures' => MeasuresReportService::class,
        'diabetes-type' => DiabetesTypeReportService::class,
        'appointment-status' => AppointmentStatusReportService::class
        // Agrega más tipos de reportes y sus servicios correspondientes aquí
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

        $serviceClass = $this->reportServices[$reportType];
        $service = new $serviceClass();
        $reportData = $service->generate($request->all());

        $chartImage = $request->input('chartImage'); 

        $dataForPdf = [
            'title' => $reportData['reportTitle'] ?? 'Reporte',
            'tableData' => $reportData['tableData'] ?? [],
            'chartImage' => $chartImage,
            'stats' => $reportData['stats'] ?? [],
            'filters' => $reportData['filters'] ?? [],
        ];
        // dd($dataForPdf);
        $pdf = Pdf::loadView('pdf_template', $dataForPdf);

        return $pdf->download("reporte-{$reportType}.pdf");
    }
}
