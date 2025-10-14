<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
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

    public function imageable()
    {
        return $this->morphTo();
    }
}
