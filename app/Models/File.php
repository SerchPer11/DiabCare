<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'filename',
        'path',
        'size',
        'mime_type',
        'file_type_id',
        'fileable_id',
        'fileable_type',
    ];

    public function fileType()
    {
        return $this->belongsTo(FileType::class);
    }

    public function fileable()
    {
        return $this->morphTo();
    }
}
