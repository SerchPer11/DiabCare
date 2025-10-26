<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'filename',
        'path',
        'size',
        'mime_type',
        'file_type_id',
        'fileable_id',
        'fileable_type',
    ];

    public function getUrlAttribute()
    {
        if ($this->isPrivate()) {
            return URL::signedRoute('file.serve', $this->id, now()->addMinutes(60));
        }

        if ($this->isPublic()) {
            return Storage::url($this->path);
        }

        return null;
    }

    public function isPrivate(): bool
    {
        return Storage::disk('private')->exists($this->path);
    }

    public function isPublic(): bool
    {
        return Storage::disk('public')->exists($this->path);
    }

    // Relación con poliformifica (1 a m)
    public function fileable()
    {
        return $this->morphTo();
    }
}
