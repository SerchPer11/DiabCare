<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class FixBackupStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:fix-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix inconsistent backup statuses in the database';

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
        $this->info('Checking and fixing backup statuses...');

        try {
            $fixed = $this->backupService->fixBackupStatuses();
            
            if ($fixed > 0) {
                $this->info("Fixed {$fixed} backup(s) with inconsistent statuses.");
            } else {
                $this->info('All backup statuses are consistent. No fixes needed.');
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error fixing backup statuses: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
