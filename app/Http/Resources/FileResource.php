<?php

namespace App\Http\Resources;

use App\Traits\DateFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FileResource extends JsonResource
{
    use DateFormat;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'description'       => $this->description,
            'filename'          => $this->filename,
            'path'              => $this->url,
            'size'              => $this->size,
            'mime_type'         => $this->mime_type,
            // 'file_type_id'      => $this->file_type_id,
            // 'type'              => new FileTypeResource($this->whenLoaded('fileType')),
            'created_at'        => $this->textFormatDate($this->created_at),
        ];
    }
}
