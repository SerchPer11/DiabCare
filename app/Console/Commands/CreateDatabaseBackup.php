<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateDatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:create 
                            {--description= : Optional description for the backup}
                            {--user= : User ID who is creating the backup (defaults to first admin)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a database backup automatically';

    /**
     * The backup service instance.
     */
    protected BackupService $backupService;

    /**
     * Create a new command instance.
     */
    public function __construct(BackupService $backupService)
    {
        parent::__construct();
        $this->backupService = $backupService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting database backup creation...');

        try {
            // Set the user who is creating the backup
            $userId = $this->option('user');
            if (!$userId) {
                // Default to first admin user
                $adminUser = User::role('admin')->first();
                if (!$adminUser) {
                    $this->error('No admin user found. Please specify a user ID or create an admin user first.');
                    return Command::FAILURE;
                }
                $userId = $adminUser->id;
            }

            // Set the authenticated user for the backup service
            Auth::login(User::find($userId));

            $description = $this->option('description') ?: 'Automatic backup created via command line';

            $this->info('Creating backup with description: ' . $description);

            // Create the backup
            $backup = $this->backupService->createBackup($description);

            if ($backup->isCompleted()) {
                $this->info('✅ Backup created successfully!');
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['ID', $backup->id],
                        ['Filename', $backup->filename],
                        ['Size', $backup->formatted_size],
                        ['Status', ucfirst($backup->status)],
                        ['Created At', $backup->created_at->format('Y-m-d H:i:s')],
                        ['Description', $backup->description ?: 'N/A'],
                    ]
                );

                Log::info('Database backup created via command', [
                    'backup_id' => $backup->id,
                    'filename' => $backup->filename,
                    'user_id' => $userId
                ]);

                return Command::SUCCESS;

            } else {
                $this->error('❌ Backup creation failed. Status: ' . $backup->status);
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error('❌ Error creating backup: ' . $e->getMessage());
            
            Log::error('Failed to create backup via command', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }
}
