<?php

namespace App\Http\Requests\Traits;

trait PhotoRules
{
    public function photoRules(int $limit = 5, int $size = 2048): array
    {
        return [
            'photos'               => ['array', 'max:' . $limit],
            'photos.*.id'          => ['nullable', 'integer', 'exists:photos,id'],
            'photos.*.file'        => ['required_without:photos.*.id', 'image', 'max:' . $size],
            'photos.*.title'       => ['required', 'string', 'max:255'],
            'photos.*.description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function photoAttributes(): array
    {
        return [
            'photos'               => 'fotos',
            // 'photos.*.id'          => 'id',
            'photos.*.file'        => 'archivo de foto',
            'photos.*.title'       => 'título de la foto',
            'photos.*.description' => 'descripción de la foto',
        ];
    }
}
