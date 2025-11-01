<?php

namespace App\Http\Resources\Forum;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'user_id' => $this->user_id,
            'forum_status_id' => $this->forum_status_id,
            'user_id' => $this->user_id,
            'posted_at' => $this->created_at->format('d/m/Y'),
            'event_time' => $this->created_at->format('H:i'),
            'category_id' => $this->category_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'answers' => ForumAnswerResource::collection($this->whenLoaded('answers')),
            'status' => new ForumStatusResource($this->whenLoaded('status')),
            'category' => new ForumCategoryResource($this->whenLoaded('category')),
            'answers_count' => $this->answers_count,
        ];
    }
}
