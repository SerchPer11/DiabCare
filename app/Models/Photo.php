<?php

namespace App\Models;

use App\Enums\PhotoUsage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Photo extends Model
{
    use HasFactory;

    protected $table = 'photos';

    protected $fillable = [
        'title',
        'description',
        'usage',
        'filename',
        'path',
        'mime_type',
        'size',
        'photoable_id',
        'photoable_type',
    ];

    protected $appends = ['url'];

    protected $casts = [
        'usage' => PhotoUsage::class,
    ];

    public function getUrlAttribute()
    {
        if ($this->isPrivate()) {
            return URL::signedRoute('photo.serve', $this->id, now()->addMinutes(60));
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

    public function photoable(): MorphTo
    {
        return $this->morphTo();
    }
}
