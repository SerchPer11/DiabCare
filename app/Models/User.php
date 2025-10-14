<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\PatienteProfile;
use App\Models\DoctorProfile;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Image;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'phone',
        'gender',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function patienteProfile()
    {
        return $this->hasOne(PatienteProfile::class);
    }

    public function doctorProfile()
    {
        return $this->hasOne(DoctorProfile::class);
    }
    
    public function image(){
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * Get user roles as array of role names
     */
    public function getRolesArray(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Get user permissions as array of permission names
     */
    public function getPermissionArray(): array
    {
        return $this->getAllPermissions()->pluck('name')->toArray();
    }
}
