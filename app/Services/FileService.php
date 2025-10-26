<?php

namespace App\Services;

use App\DTOs\FileStorageConfig;
use App\Models\File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public function createFile(UploadedFile $file, string $disk = 'private', string $basePath = 'files'): array
    {
        $uuid = Str::uuid();
        $extension = $file->getClientOriginalExtension();
        $filename = "{$uuid}.{$extension}";
        $filePath = $file->storeAs($basePath, $filename, $disk);

        return [
            'filename' => $filename,
            'path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ];
    }

    public function storeFile(Model $parent, array $fileData, FileStorageConfig $config): File
    {
        $fileMetaData = $this->createFile($fileData['file'], $config->disk, $config->basePath);
        $mergedData = array_merge(Arr::except($fileData, 'file'), $fileMetaData);
        $file = $parent->{$config->relation}()->create($mergedData);
        return $file;
    }

    public function storeFiles(Model $parent, array $filesData = [], FileStorageConfig $config): void
    {
        if (empty($filesData)) return;

        foreach ($filesData as $fileData) {
            if (isset($fileData['file']) && $fileData['file'] instanceof UploadedFile) {
                $this->storeFile($parent, $fileData, $config);
            }
        }
    }

    public function updateFile(array $fileData): File
    {
        $file = File::find($fileData['id']);
        $file->update($fileData);
        return $file->fresh();
    }

    public function replaceFile(File $file, UploadedFile $newFile, FileStorageConfig $config): File
    {
        $this->deleteFile($file);
        $newFileData = $this->createFile($newFile, $config->disk, $config->basePath);
        $file->update($newFileData);

        return $file->fresh();
    }

    public function deleteFile(File $file, string $disk = 'private', bool $deleteRecord = true): void
    {
        Storage::disk($disk)->delete($file->path);

        if ($deleteRecord) {
            $file->delete();
        }
    }

    public function deleteFiles(Collection $files, string $disk, bool $deleteRecords = true): void
    {
        foreach ($files as $file) {
            $this->deleteFile($file, $disk, $deleteRecords);
        }
    }

    public function syncFiles(Model $parent, array $filesData, FileStorageConfig $config): void
    {
        $fileIdsInRequest = collect($filesData)
            ->filter(fn($fileData) => isset($fileData['id']))
            ->pluck('id')
            ->toArray();

        $existingFiles = $parent->{$config->relation}()->get();

        $filesToDelete = $existingFiles->whereNotIn('id', $fileIdsInRequest);
        $this->deleteFiles($filesToDelete, $config->disk);

        foreach ($filesData as $fileData) {
            if (isset($fileData['id'])) {
                $this->updateFile($fileData);
            } else {
                if (isset($fileData['file'])) {
                    $this->storeFile($parent, $fileData, $config);
                }
            }
        }
    }
}
