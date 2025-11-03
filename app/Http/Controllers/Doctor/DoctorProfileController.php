<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Requests\Doctor\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Doctor\DoctorProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Doctor\Specialty;
use Inertia\Response;
use Inertia\Inertia;
use App\Models\User;


class DoctorProfileController extends Controller
{
    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'doctor.profile.';
        $this->source = 'Doctor/Profile/Pages/';

        $this->middleware("permission:{$this->routeName}show")->only(['profile']);
        $this->middleware("permission:{$this->routeName}edit")->only(['update']); 
    }

    public function profile(): Response
    {
        $user = User::with(['profileable', 'roles'])->find(Auth::user()->id);

        return Inertia::render("{$this->source}Profile", [
            'title'     => 'Mi Perfil',
            'routeName' => $this->routeName,
            'specialties' => Specialty::all()->sortBy('name'),
            'profile'   => new UserResource($user),
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = User::find(Auth::user()->id);

        $validatedData = $request->validated();

        DB::Transaction(function () use ($user, $validatedData, $request) {
            $user->update([
                'name' => $validatedData['name'],
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'] ?? null,
                'gender' => $validatedData['gender'] ?? null,
                'birthdate' => $validatedData['birthdate'],
            ]);

            if($request->filled('password')){
                $user->update([
                    'password' => Hash::make($validatedData['password']),
                ]);
            }

            if ($user->profileable) {
                $user->profileable->update([
                    'specialty_id' => $validatedData['specialty_id'],
                    'license_number' => $validatedData['license_number'],
                    'titulation_date' => $validatedData['titulation_date'],

                ]);
            } else {
                $profile = DoctorProfile::create([
                    'specialty_id' => $validatedData['specialty_id'],
                    'license_number' => $validatedData['license_number'],
                    'titulation_date' => $validatedData['titulation_date'],
                ]);

                $user->profileable()->associate($profile);
                $user->save();
            }
        });

        return redirect()->route("{$this->routeName}show")
            ->with('success', 'Información actualizada correctamente.');
    }
}
