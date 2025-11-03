<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Resources\NotificationResource;

class NotificationController extends Controller
{
    protected $model;
    protected $routeName;
    protected $source; 

    public function __construct()
    {
        $this->routeName = 'notifications';
    }

    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());

        // 2. El query INICIA DESDE EL USUARIO. Esto es clave.
        $query = $request->user()->notifications(); // Esto reemplaza a $this->model

        // 3. Aplicamos la búsqueda (adaptada para la columna 'data' de JSON)
        $query->when($filters->search, function ($query, $search) {
            $query->where('data->message', 'LIKE', '%' . $search . '%');
            // Nota: No podemos hacer 'orWhereHas' porque no hay relaciones directas
        });

        // 4. Aplicamos orden y paginación
        $notifications = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        // 5. Devolvemos la vista Inertia
        return Inertia::render('Notifications/Index', [
            'title'         => 'Notificaciones',
            'notifications' => NotificationResource::collection($notifications), // <-- Usamos un Resource
            'routeName'     => $this->routeName,
            'filters'       => $filters
        ]);
    }

    /**
     * Define los filtros y valores por defecto para este controlador.
     */
    protected function getFiltersBase(array $filters): object
    {
        return (object) [
            'search'    => $filters['search'] ?? '',
            'order'     => $filters['order'] ?? 'created_at', // Orden por defecto
            'direction' => $filters['direction'] ?? 'desc', // Más nuevas primero
            'rows'      => $filters['rows'] ?? 15,
        ];
    }

    /**
     * Marca una notificación como leída.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['status' => 'success']);
    }
}
