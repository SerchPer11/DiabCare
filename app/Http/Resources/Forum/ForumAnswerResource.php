<?php

namespace App\Http\Resources\Forum;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class ForumAnswerResource extends JsonResource
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
            'answer' => $this->answer,
            'forum_id' => $this->forum_id,
            'user_id' => $this->user_id,
            'comment_time' => $this->created_at->format('H:i'),
            'posted_at' => $this->created_at->format('d/m/Y'),
            'user' => new UserResource($this->whenLoaded('user')),
            'question' => new ForumResource($this->whenLoaded('forum')),
        ];
    }
}
