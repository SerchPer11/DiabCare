<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Backup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'filename',
        'path',
        'size',
        'checksum',
        'status',
        'description',
        'created_by',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'size' => 'integer',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created this backup.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the file size in a human readable format.
     */
    public function getFormattedSizeAttribute(): string
    {
        if (!$this->size || $this->size == 0) {
            // Try different path options
            $possiblePaths = [
                $this->path,
                storage_path('app/backups/' . $this->filename),
                storage_path('app/' . $this->path),
            ];
            
            foreach ($possiblePaths as $path) {
                if ($path && file_exists($path)) {
                    $actualSize = filesize($path);
                    if ($actualSize > 0) {
                        // Update the size in the database silently
                        $this->updateQuietly(['size' => $actualSize]);
                        return $this->formatBytes($actualSize);
                    }
                }
            }
            
            return 'N/A';
        }

        return $this->formatBytes($this->size);
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = $bytes;
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Check if the backup is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the backup failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the backup is corrupted.
     */
    public function isCorrupted(): bool
    {
        return $this->status === 'corrupted';
    }

    /**
     * Scope to get only completed backups.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query;
    }
}
