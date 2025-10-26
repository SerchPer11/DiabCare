<?php

namespace App\Http\Requests\Traits;

trait FileValidationRules
{
    protected function filesRules(int $limit = 5, int $size = 2048): array
    {
        return [
            'files' => ['array', 'nullable', 'max:' . $limit],
            'files.*.id' => 'nullable|exists:files,id',
            'files.*.title' => ['required', 'string', 'max:150'],
            'files.*.description' => ['required', 'string', 'max:150'],
            'files.*.file' => ['required_without:files.*.id', 'file', 'max:' . $size],
        ];
    }

    protected function filesAttributes(): array
    {
        return [
            'files' => 'archivos',
            'files.*.id' => 'id',
            'files.*.title' => 'título',
            'files.*.description' => 'descripción',
            'files.*.file' => 'archivo'
        ];
    }
}
