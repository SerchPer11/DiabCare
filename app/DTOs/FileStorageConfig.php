<?php

namespace App\DTOs;

class FileStorageConfig
{
    public function __construct(
        public string $disk = 'private',
        public string $basePath = 'files',
        public string $relation = 'files',
        public array $additionalData = []
    ) {}

    public static function create(array $config = []): self
    {
        return new self(
            disk: $config['disk'] ?? 'private',
            basePath: $config['basePath'] ?? 'files',
            relation: $config['relation'] ?? 'files',
            additionalData: $config['additionalData'] ?? []
        );
    }

    public function withDisk(string $disk): self
    {
        return new self($disk, $this->basePath, $this->relation, $this->additionalData);
    }

    public function withPath(string $basePath): self
    {
        return new self($this->disk, $basePath, $this->relation, $this->additionalData);
    }

    public function withRelation(string $relation): self
    {
        return new self($this->disk, $this->basePath, $relation, $this->additionalData);
    }

    public function withAdditionalData(array $data): self
    {
        return new self($this->disk, $this->basePath, $this->relation, array_merge($this->additionalData, $data));
    }

    public function toArray(): array
    {
        return [
            'disk' => $this->disk,
            'basePath' => $this->basePath,
            'relation' => $this->relation,
            'additionalData' => $this->additionalData
        ];
    }
}