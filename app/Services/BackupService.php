<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BackupService
{
    /**
     * The path where backups are stored.
     */
    protected string $backupPath;

    /**
     * Maximum number of backups to keep.
     */
    protected int $maxBackups;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        $this->maxBackups = config('backup.max_backups', 10);
        
        // Create backup directory if it doesn't exist
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Create a new database backup.
     */
    public function createBackup(string $description = null): Backup
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "diabcare_backup_{$timestamp}.sql";
        $filepath = $this->backupPath . DIRECTORY_SEPARATOR . $filename;

        // Create backup record
        $backup = Backup::create([
            'filename' => $filename,
            'path' => $filepath,
            'status' => 'pending',
            'description' => $description,
            'created_by' => Auth::id(),
        ]);

        try {
            // Generate database backup
            $this->generateDatabaseBackup($filepath);

            // Calculate file size and checksum
            $size = File::size($filepath);
            $checksum = hash_file('sha256', $filepath);

            // Update backup record
            $backup->update([
                'size' => $size,
                'checksum' => $checksum,
                'status' => 'completed',
                'completed_at' => Carbon::now(),
            ]);

            // Clean old backups
            $this->cleanOldBackups();

            Log::info('Database backup created successfully', [
                'backup_id' => $backup->id,
                'filename' => $filename,
                'size' => $size
            ]);

        } catch (\Exception $e) {
            $backup->update(['status' => 'failed']);
            
            Log::error('Failed to create database backup', [
                'backup_id' => $backup->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }

        return $backup;
    }

    /**
     * Generate the actual database backup using mysqldump.
     */
    protected function generateDatabaseBackup(string $filepath): void
    {
        $database = config('database.connections.mysql');
        
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s --single-transaction --routines --triggers %s > %s',
            escapeshellarg($database['username']),
            escapeshellarg($database['password']),
            escapeshellarg($database['host']),
            escapeshellarg($database['port']),
            escapeshellarg($database['database']),
            escapeshellarg($filepath)
        );

        $returnVar = 0;
        $output = [];
        
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('Failed to create database backup. Return code: ' . $returnVar);
        }

        if (!File::exists($filepath) || File::size($filepath) === 0) {
            throw new \Exception('Backup file was not created or is empty');
        }
    }

    /**
     * Restore database from backup.
     */
    public function restoreBackup(Backup $backup): bool
    {
        if (!$backup->isCompleted()) {
            throw new \Exception('Cannot restore incomplete backup');
        }

        if (!File::exists($backup->path)) {
            $backup->update(['status' => 'corrupted']);
            throw new \Exception('Backup file not found');
        }

        // Verify backup integrity
        if (!$this->verifyBackupIntegrity($backup)) {
            $backup->update(['status' => 'corrupted']);
            throw new \Exception('Backup file is corrupted');
        }

        // Guardar información del usuario actual antes de la restauración
        $currentUserId = Auth::id();
        $backupId = $backup->id;

        try {
            // Guardar información original del backup antes de la restauración
            $originalSize = $backup->size;
            $originalChecksum = $backup->checksum;
            $originalCompletedAt = $backup->completed_at;

            // Create a backup before restoration (safety backup)
            $safetyBackup = $this->createBackup('Automatic backup before restoration');

            // Restore the database
            $this->restoreDatabase($backup->path);

            // Después de la restauración, necesitamos reautenticar y actualizar el estado
            // Re-autenticar el usuario si existe
            if ($currentUserId) {
                $user = \App\Models\User::find($currentUserId);
                if ($user) {
                    Auth::login($user);
                }
            }

            // Ejecutar arreglo de estados después de la restauración
            $this->fixBackupStatuses();

            // Como último recurso, asegurar que el backup original mantenga su información
            $restoredBackup = Backup::find($backupId);
            if ($restoredBackup && File::exists($restoredBackup->path)) {
                $currentSize = File::size($restoredBackup->path);
                $currentChecksum = hash_file('sha256', $restoredBackup->path);
                
                $restoredBackup->update([
                    'status' => 'completed',
                    'size' => $currentSize,
                    'checksum' => $currentChecksum,
                    'completed_at' => $originalCompletedAt ?? $restoredBackup->created_at
                ]);
            }

            Log::info('Database restored successfully', [
                'backup_id' => $backupId,
                'safety_backup_id' => $safetyBackup->id,
                'user_id' => $currentUserId,
                'original_size' => $originalSize,
                'restored_size' => $restoredBackup->size ?? 'unknown'
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to restore database', [
                'backup_id' => $backupId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Restore database using mysql command.
     */
    protected function restoreDatabase(string $filepath): void
    {
        $database = config('database.connections.mysql');
        
        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s --port=%s %s < %s',
            escapeshellarg($database['username']),
            escapeshellarg($database['password']),
            escapeshellarg($database['host']),
            escapeshellarg($database['port']),
            escapeshellarg($database['database']),
            escapeshellarg($filepath)
        );

        $returnVar = 0;
        $output = [];
        
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('Failed to restore database. Return code: ' . $returnVar);
        }
    }

    /**
     * Verify backup file integrity using checksum.
     */
    public function verifyBackupIntegrity(Backup $backup): bool
    {
        if (!File::exists($backup->path)) {
            return false;
        }

        if (!$backup->checksum) {
            return false;
        }

        $currentChecksum = hash_file('sha256', $backup->path);
        return $currentChecksum === $backup->checksum;
    }

    /**
     * Download backup file.
     */
    public function downloadBackup(Backup $backup)
    {
        if (!File::exists($backup->path)) {
            throw new \Exception('Backup file not found');
        }

        return response()->download($backup->path, $backup->filename);
    }

    /**
     * Delete old backups to maintain storage limits.
     */
    protected function cleanOldBackups(): void
    {
        $oldBackups = Backup::where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->skip($this->maxBackups)
            ->take(100)
            ->get();

        foreach ($oldBackups as $backup) {
            $this->deleteBackup($backup);
        }
    }

    /**
     * Delete a backup and its file.
     */
    public function deleteBackup(Backup $backup): bool
    {
        try {
            // Delete physical file
            if (File::exists($backup->path)) {
                File::delete($backup->path);
            }

            // Delete database record
            $backup->delete();

            Log::info('Backup deleted successfully', ['backup_id' => $backup->id]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to delete backup', [
                'backup_id' => $backup->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Get backup statistics.
     */
    public function getBackupStats(): array
    {
        return [
            'total_backups' => Backup::count(),
            'completed_backups' => Backup::where('status', 'completed')->count(),
            'failed_backups' => Backup::where('status', 'failed')->count(),
            'total_size' => Backup::where('status', 'completed')->sum('size'),
            'latest_backup' => Backup::where('status', 'completed')->latest()->first(),
            'storage_used' => $this->getStorageUsed(),
        ];
    }

    /**
     * Get total storage used by backups.
     */
    protected function getStorageUsed(): int
    {
        $totalSize = 0;
        $files = File::files($this->backupPath);
        
        foreach ($files as $file) {
            $totalSize += $file->getSize();
        }

        return $totalSize;
    }

    /**
     * Validate backup before restoration.
     */
    public function validateBackupForRestore(Backup $backup): array
    {
        $errors = [];

        if (!$backup->isCompleted()) {
            $errors[] = 'El respaldo no está completo';
        }

        if (!File::exists($backup->path)) {
            $errors[] = 'El archivo de respaldo no existe';
        }

        if (!$this->verifyBackupIntegrity($backup)) {
            $errors[] = 'El respaldo está corrupto';
        }

        if (File::size($backup->path) === 0) {
            $errors[] = 'El archivo de respaldo está vacío';
        }

        return $errors;
    }

    /**
     * Fix backup statuses that may have become inconsistent.
     */
    public function fixBackupStatuses(): int
    {
        $fixed = 0;
        
        // Encontrar backups que tienen archivo válido pero están marcados como pending
        $pendingBackups = Backup::where('status', 'pending')->get();

        foreach ($pendingBackups as $backup) {
            if (File::exists($backup->path) && File::size($backup->path) > 0) {
                $actualSize = File::size($backup->path);
                $actualChecksum = hash_file('sha256', $backup->path);
                
                // Actualizar información del backup
                $backup->update([
                    'status' => 'completed',
                    'size' => $actualSize,
                    'checksum' => $actualChecksum,
                    'completed_at' => $backup->completed_at ?? $backup->created_at
                ]);
                
                $fixed++;
                Log::info('Fixed backup status and updated info', [
                    'backup_id' => $backup->id,
                    'size' => $actualSize,
                    'checksum' => $actualChecksum
                ]);
            }
        }

        // Encontrar backups con información incompleta pero archivo válido
        $incompleteBackups = Backup::where(function($query) {
            $query->whereNull('size')
                  ->orWhereNull('checksum')
                  ->orWhere('size', 0);
        })->get();

        foreach ($incompleteBackups as $backup) {
            if (File::exists($backup->path) && File::size($backup->path) > 0) {
                $actualSize = File::size($backup->path);
                $actualChecksum = hash_file('sha256', $backup->path);
                
                $backup->update([
                    'status' => 'completed',
                    'size' => $actualSize,
                    'checksum' => $actualChecksum,
                    'completed_at' => $backup->completed_at ?? $backup->created_at
                ]);
                
                $fixed++;
                Log::info('Fixed incomplete backup info', [
                    'backup_id' => $backup->id,
                    'size' => $actualSize
                ]);
            }
        }

        // Verificar backups que están marcados como completed pero el archivo no existe
        $completedBackups = Backup::where('status', 'completed')->get();
        foreach ($completedBackups as $backup) {
            if (!File::exists($backup->path)) {
                $backup->update(['status' => 'corrupted']);
                Log::warning('Marked backup as corrupted due to missing file', ['backup_id' => $backup->id]);
                $fixed++;
            }
        }

        return $fixed;
    }

    /**
     * Upload and process a backup file.
     */
    public function uploadBackup($uploadedFile, ?string $description = null): Backup
    {
        try {
            // Generate unique filename
            $timestamp = now()->format('Y-m-d_H-i-s');
            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = "uploaded_{$originalName}_{$timestamp}.sql";
            
            // Ensure backup directory exists
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $path = $backupDir . '/' . $filename;

            // Move uploaded file to backup directory
            $uploadedFile->move($backupDir, $filename);

            // Calculate file size and checksum
            $size = File::size($path);
            $checksum = hash_file('sha256', $path);

            // Validate it's a SQL file by checking content
            $this->validateSqlFile($path);

            // Create backup record
            $backup = Backup::create([
                'filename' => $filename,
                'path' => $path,
                'size' => $size,
                'checksum' => $checksum,
                'status' => 'completed',
                'description' => $description ?: 'Respaldo subido manualmente',
                'created_by' => Auth::id(),
                'completed_at' => now(),
            ]);

            Log::info('Backup uploaded successfully', [
                'backup_id' => $backup->id,
                'filename' => $filename,
                'size' => $size
            ]);

            return $backup;

        } catch (\Exception $e) {
            // Clean up file if something went wrong
            if (isset($path) && File::exists($path)) {
                File::delete($path);
            }

            Log::error('Backup upload failed', [
                'error' => $e->getMessage(),
                'original_filename' => $uploadedFile->getClientOriginalName()
            ]);

            throw new \RuntimeException('Error al procesar el archivo de respaldo: ' . $e->getMessage());
        }
    }

    /**
     * Validate uploaded file is a valid SQL backup for DiabCare.
     */
    protected function validateSqlFile(string $filePath): void
    {
        if (!File::exists($filePath)) {
            throw new \InvalidArgumentException('El archivo no existe');
        }

        // Read a larger portion of the file for better validation
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \InvalidArgumentException('No se puede leer el archivo');
        }

        $content = fread($handle, 32768); // Read first 32KB for better analysis
        fclose($handle);

        if (empty($content)) {
            throw new \InvalidArgumentException('El archivo está vacío');
        }

        // Check for common SQL patterns
        $sqlPatterns = [
            '/CREATE\s+TABLE/i',
            '/INSERT\s+INTO/i',
            '/DROP\s+TABLE/i',
            '/USE\s+\w+/i',
            '/SET\s+/i',
            '/mysqldump/i',
            '/MySQL\s+dump/i',
            '/MariaDB\s+dump/i'
        ];

        $hasValidSql = false;
        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $hasValidSql = true;
                break;
            }
        }

        if (!$hasValidSql) {
            throw new \InvalidArgumentException('El archivo no parece ser un respaldo SQL válido');
        }

        // Check for database name compatibility
        $currentDbName = config('database.connections.mysql.database');
        $dbNameInFile = null;
        
        // Look for database name in the backup
        if (preg_match('/USE\s+`?(\w+)`?/i', $content, $matches)) {
            $dbNameInFile = $matches[1];
        } elseif (preg_match('/CREATE\s+DATABASE.*`?(\w+)`?/i', $content, $matches)) {
            $dbNameInFile = $matches[1];
        }

        // Check for DiabCare-specific table structures to ensure compatibility
        $diabCareTablesRequired = [
            'users',
            'modules', 
            'permissions',
            'roles',
            'patient_profiles'
        ];

        $diabCareTablesFound = 0;
        $tablesFoundList = [];
        
        foreach ($diabCareTablesRequired as $table) {
            if (preg_match('/CREATE\s+TABLE.*`?' . $table . '`?/i', $content) || 
                preg_match('/INSERT\s+INTO.*`?' . $table . '`?/i', $content)) {
                $diabCareTablesFound++;
                $tablesFoundList[] = $table;
            }
        }

        // If it doesn't have at least 3 core DiabCare tables, it's likely from another system
        if ($diabCareTablesFound < 3) {
            throw new \InvalidArgumentException(
                'Este archivo parece ser de otra base de datos. ' .
                'Solo se pueden subir respaldos compatibles con DiabCare. ' .
                'Tablas DiabCare encontradas: ' . implode(', ', $tablesFoundList) . 
                ' (' . $diabCareTablesFound . '/5 requeridas). ' .
                ($dbNameInFile ? 'Base de datos origen: ' . $dbNameInFile : 'No se detectó nombre de base de datos.')
            );
        }

        // Additional validation: check for potentially dangerous operations
        $dangerousPatterns = [
            '/DROP\s+DATABASE/i',
            '/CREATE\s+DATABASE/i',
            '/GRANT\s+/i',
            '/REVOKE\s+/i',
            '/ALTER\s+USER/i',
            '/CREATE\s+USER/i',
            '/DROP\s+USER/i'
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                Log::warning('Potentially dangerous SQL detected in upload', [
                    'pattern' => $pattern,
                    'file' => $filePath
                ]);
                // Note: We log but don't reject - these might be legitimate in some backups
            }
        }

        // Check file size (additional safety)
        $size = File::size($filePath);
        if ($size > 500 * 1024 * 1024) { // 500MB
            throw new \InvalidArgumentException('El archivo es demasiado grande (máximo 500MB)');
        }

        if ($size < 100) { // Less than 100 bytes is suspicious
            throw new \InvalidArgumentException('El archivo es demasiado pequeño para ser un respaldo válido');
        }

        Log::info('SQL file validation passed', [
            'file' => $filePath,
            'size' => $size,
            'diabcare_tables_found' => $diabCareTablesFound
        ]);
    }
}