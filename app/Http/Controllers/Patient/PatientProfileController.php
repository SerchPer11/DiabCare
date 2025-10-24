<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Patient\UpdateProfileRequest;
use App\Models\Patient\PatientProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientProfileController extends Controller
{
    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'patient.profile.';
        $this->source = 'Patient/Profile/Pages/';

        /*$this->middleware("permission:{$this->routeName}show")->only(['profile']);
        $this->middleware("permission:{$this->routeName}edit")->only(['update']); */
    }

    public function profile(): Response
    {
        $user = User::with(['profileable', 'roles'])->find(Auth::user()->id);


        return Inertia::render("{$this->source}Profile", [
            'title'     => 'Mi Perfil',
            'routeName' => $this->routeName,
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
                    'blood_type' => $validatedData['blood_type'],
                    'height' => $validatedData['height'],
                    'weight' => $validatedData['weight'],

                ]);
            } else {
                $profile = PatientProfile::create([
                    'blood_type' => $validatedData['blood_type'],
                    'height' => $validatedData['height'],
                    'weight' => $validatedData['weight'],
                ]);

                $user->profileable()->associate($profile);
                $user->save();
            }
        });

        return redirect()->route("{$this->routeName}show")
            ->with('success', 'Información actualizada correctamente.');
    }
}
