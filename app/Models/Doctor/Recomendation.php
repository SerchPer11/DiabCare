<?php

namespace App\Models\Doctor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Admin\Catalogs\RecomendationType;
use App\Models\Patient\ClinicalLog;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;
use App\Models\Photo;
use App\Models\File;

class Recomendation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'recomendations';

    protected $fillable = [
        'title',
        'recomendation_type_id',
        'priority',
        'content',
        'start_date',
        'end_date',
        'patient_id',
        'doctor_id',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Recomendation $recomendation) {
            $recomendation->clinicalLogs()->delete();

            $fragmentoLink = '/patient/recommendations/' . $recomendation->id;

            DatabaseNotification::where('type', 'App\Notifications\NewRecommendation')
                ->where('data->link', 'like', '%' . $fragmentoLink . '%')
                ->delete();
        });
    }

    public function clinicalLogs()
    {
        return $this->morphMany(ClinicalLog::class, 'loggable');
    }


    public function recomendationType()
    {
        return $this->belongsTo(RecomendationType::class, 'recomendation_type_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function photos()
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
