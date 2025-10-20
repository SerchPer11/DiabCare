<?php

namespace App\DTOs;

class PhotoStorageConfig
{
    public function __construct(
        public string $disk = 'private',
        public string $basePath = 'photos',
        public string $relation = 'photos',
        public string $usage = 'attachment',
        public array $additionalData = []
    ) {}

    public static function create(array $config = []): self
    {
        return new self(
            disk: $config['disk'] ?? 'private',
            basePath: $config['basePath'] ?? 'photos',
            relation: $config['relation'] ?? 'photos',
            usage: $config['usage'] ?? 'attachment',
            additionalData: $config['additionalData'] ?? []
        );
    }

    public function withDisk(string $disk): self
    {
        return new self($disk, $this->basePath, $this->relation, $this->usage, $this->additionalData);
    }

    public function withPath(string $basePath): self
    {
        return new self($this->disk, $basePath, $this->relation, $this->usage, $this->additionalData);
    }

    public function withRelation(string $relation): self
    {
        return new self($this->disk, $this->basePath, $relation, $this->usage, $this->additionalData);
    }

    public function withUsage(string $usage): self
    {
        return new self($this->disk, $this->basePath, $this->relation, $usage, $this->additionalData);
    }

    public function withAdditionalData(array $data): self
    {
        return new self($this->disk, $this->basePath, $this->relation, $this->usage, array_merge($this->additionalData, $data));
    }

    public function toArray(): array
    {
        return [
            'disk' => $this->disk,
            'basePath' => $this->basePath,
            'relation' => $this->relation,
            'usage' => $this->usage,
            'additionalData' => $this->additionalData
        ];
    }
}
