<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BackupResource;
use App\Models\Backup;
use App\Services\BackupService;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BackupController extends Controller
{
    use Filterable;

    protected string $routeName;
    protected string $source;
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->routeName = 'backups.';
        $this->source = 'Admin/Backups/Pages/';
        $this->backupService = $backupService;

        // Set middleware for specific routes
        $this->middleware("permission:{$this->routeName}index")->only(['index']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}restore")->only(['restore', 'confirmRestore']);
        $this->middleware("permission:{$this->routeName}download")->only(['download']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
    }

    /**
     * Display a listing of backups.
     */
    public function index(Request $request)
    {
        // Auto-fix any inconsistent backup statuses
        $this->backupService->fixBackupStatuses();
        
        $filters = $this->getFiltersBase($request->query());
        
        // Add date range filters
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Backup::with('creator')
            ->when($filters->search, function ($query, $search) {
                $query->where('filename', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('creator', function ($query) use ($search) {
                        $query->where('name', 'LIKE', '%' . $search . '%');
                    });
            })
            ->dateRange($startDate, $endDate);

        $backups = $query->orderBy($filters->order ?? 'created_at', $filters->direction ?? 'desc')
            ->paginate($filters->rows)
            ->withQueryString();

        $stats = $this->backupService->getBackupStats();

        return Inertia::render("{$this->source}Index", [
            'title' => 'Respaldos de Base de Datos',
            'backups' => BackupResource::collection($backups),
            'stats' => $stats,
            'routeName' => $this->routeName,
            'filters' => array_merge((array) $filters, [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ])
        ]);
    }

    /**
     * Show the form for creating a new backup.
     */
    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title' => 'Crear Respaldo',
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Store a newly created backup.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $backup = $this->backupService->createBackup($request->description);
            
            return redirect()
                ->route("{$this->routeName}index")
                ->with('success', 'Respaldo creado exitosamente');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el respaldo: ' . $e->getMessage());
        }
    }

    /**
     * Show the restore confirmation page.
     */
    public function restore(Backup $backup)
    {
        // Validate backup before showing restore page
        $errors = $this->backupService->validateBackupForRestore($backup);

        if (!empty($errors)) {
            return redirect()
                ->route("{$this->routeName}index")
                ->with('error', 'No se puede restaurar este respaldo: ' . implode(', ', $errors));
        }

        return Inertia::render("{$this->source}Restore", [
            'title' => 'Restaurar Respaldo',
            'backup' => (new BackupResource($backup->load('creator')))->resolve(),
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Confirm and execute the restore operation.
     */
    public function confirmRestore(Request $request, Backup $backup)
    {
        $request->validate([
            'confirmation' => 'required|string|in:CONFIRMAR',
        ], [
            'confirmation.in' => 'Debe escribir "CONFIRMAR" exactamente para proceder.',
        ]);

        try {
            $this->backupService->restoreBackup($backup);
            
            // Ejecutar arreglo de estados adicional después de la restauración
            $fixed = $this->backupService->fixBackupStatuses();
            
            $message = 'Base de datos restaurada exitosamente.';
            if ($fixed > 0) {
                $message .= " Se corrigieron {$fixed} estados de respaldo.";
            }
            
            return redirect()
                ->route("{$this->routeName}index")
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al restaurar la base de datos: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file.
     */
    public function download(Backup $backup)
    {
        try {
            return $this->backupService->downloadBackup($backup);
            
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al descargar el respaldo: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified backup.
     */
    public function destroy(Backup $backup)
    {
        try {
            $this->backupService->deleteBackup($backup);
            
            return redirect()
                ->route("{$this->routeName}index")
                ->with('success', 'Respaldo eliminado exitosamente');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al eliminar el respaldo: ' . $e->getMessage());
        }
    }

    /**
     * Get backup integrity check.
     */
    public function checkIntegrity(Backup $backup)
    {
        $isValid = $this->backupService->verifyBackupIntegrity($backup);
        
        if (!$isValid) {
            $backup->update(['status' => 'corrupted']);
        }

        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? 'El respaldo está íntegro' : 'El respaldo está corrupto'
        ]);
    }

    /**
     * Fix backup statuses.
     */
    public function fixStatuses()
    {
        try {
            $fixed = $this->backupService->fixBackupStatuses();
            
            return redirect()
                ->route("{$this->routeName}index")
                ->with('success', "Se corrigieron {$fixed} respaldos con estados inconsistentes.");

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al corregir los estados: ' . $e->getMessage());
        }
    }

    /**
     * Show upload backup form.
     */
    public function showUpload()
    {
        return Inertia::render("{$this->source}Upload", [
            'title' => 'Subir Respaldo',
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Handle backup file upload.
     */
    public function upload(Request $request)
    {


        // Validación personalizada para archivos SQL
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:512000', // 500MB max
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        $fail('Debe seleccionar un archivo.');
                        return;
                    }
                    
                    $extension = strtolower($value->getClientOriginalExtension());
                    $allowedExtensions = ['sql'];
                    
                    if (!in_array($extension, $allowedExtensions)) {
                        $fail('El archivo debe tener extensión .sql');
                    }
                }
            ],
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $backup = $this->backupService->uploadBackup($request->file('file'), $request->description);
            

            
            return redirect()
                ->route("{$this->routeName}index")
                ->with('success', 'Respaldo subido exitosamente');

        } catch (\Exception $e) {

            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al subir el respaldo: ' . $e->getMessage());
        }
    }
}
