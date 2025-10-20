<?php

namespace App\Services;

use App\DTOs\PhotoStorageConfig;
use App\Models\Photo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoService
{
    public function createPhoto(UploadedFile $photo, PhotoStorageConfig $config)
    {
        $uuid = Str::uuid();
        $extension = $photo->getClientOriginalExtension();
        $filename = "{$config->usage}_{$uuid}.{$extension}";
        $filePath = $photo->storeAs($config->basePath, $filename, $config->disk);

        return [
            'usage' => $config->usage,
            'mime_type' => $photo->getMimeType(),
            'size' => $photo->getSize(),
            'path' => $filePath,
            'filename' => $filename
        ];
    }

    public function storePhoto(Model $parent, array $photoData, PhotoStorageConfig $config): Photo
    {
        $photoMetaData = $this->createPhoto($photoData['file'], $config);
        $mergedData = array_merge(Arr::except($photoData, 'file'), $photoMetaData);
        $photo = $parent->{$config->relation}()->create($mergedData);
        return $photo;
    }

    public function storePhotos(Model $parent, array $photosData = [], PhotoStorageConfig $config): void
    {
        if (empty($photosData)) return;

        foreach ($photosData as $photoData) {
            if (isset($photoData['file']) && $photoData['file'] instanceof UploadedFile) {
                $this->storePhoto($parent, $photoData, $config);
            }
        }
    }

    public function updatePhoto(array $photoData): Photo
    {
        $photo = Photo::find($photoData['id']);
        $photo->update($photoData);
        return $photo->fresh();
    }

    public function replacePhoto(Photo $photo, UploadedFile $newPhoto, PhotoStorageConfig $config): Photo
    {
        $this->deletePhoto($photo);
        $newPhotoData = $this->createPhoto($newPhoto, $config);
        $photo->update($newPhotoData);

        return $photo->fresh();
    }

    public function deletePhoto(Photo $photo, string $disk = 'private', bool $deleteRecord = true): void
    {
        Storage::disk($disk)->delete($photo->path);

        if ($deleteRecord) {
            $photo->delete();
        }
    }

    public function deletePhotos(Collection $photos, string $disk, bool $deleteRecords = true): void
    {
        foreach ($photos as $photo) {
            $this->deletePhoto($photo, $disk, $deleteRecords);
        }
    }

    public function syncPhotos(Model $parent, array $photosData, PhotoStorageConfig $config): void
    {
        $photoIdsInRequest = collect($photosData)
            ->filter(fn($photo) => isset($photo['id']))
            ->pluck('id')
            ->toArray();

        $existingPhotos = $parent->{$config->relation}()->get();

        $photosToDelete = $existingPhotos->whereNotIn('id', $photoIdsInRequest);
        $this->deletePhotos($photosToDelete, $config->disk);

        foreach ($photosData as $photoData) {
            if (isset($photoData['id'])) {
                $this->updatePhoto($photoData);
            } else {
                if (isset($photoData['file'])) {
                    $this->storePhoto($parent, $photoData, $config);
                }
            }
        }
    }
}
