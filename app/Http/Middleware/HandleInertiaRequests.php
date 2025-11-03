<?php

namespace App\Http\Middleware;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? new UserResource($user) : null,
                'roles' => $user ? $user->getRolesArray() : [],
                'can' => $user ? $user->getPermissionArray() : [],
                'unreadNotifications' => $user ? $user->unreadNotifications : [],
                'unreadNotificationsCount' => $user ? $user->unreadNotifications->count() : 0,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'info' => fn () => $request->session()->get('info'),
                'warning' => fn () => $request->session()->get('warning'),
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                    'name' => $request->route()->getName(), // <-- ¡Esta es la línea clave que resuelve tu error!
                ]);
            },
        ];
    }
}
