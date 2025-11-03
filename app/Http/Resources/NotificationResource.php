<?php

// app/Http/Resources/NotificationResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'message' => $this->data['message'] ?? 'Notificación sin mensaje.',
            'link' => $this->data['link'] ?? '#',
            'icon' => $this->data['icon'] ?? 'mdiBell',
            'read_at' => $this->read_at ? Carbon::parse($this->read_at)->diffForHumans() : null,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
            'created_at_raw' => $this->created_at,
        ];
    }
}
