<?php

namespace App\Actions\Freezers;

use App\Models\Freezer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateFreezerAction
{
    public function execute(
        Freezer $freezer,
        array $data,
    ): Freezer {
        /** @var UploadedFile|null $newPhoto */
        $newPhoto = $data['photo'] ?? null;

        $oldPhotoPath = $freezer->photo_path;
        $newPhotoPath = null;

        if ($newPhoto instanceof UploadedFile) {
            $newPhotoPath = $newPhoto->store(
                'freezers',
                'public',
            );
        }

        try {
            $updatedFreezer = DB::transaction(
                function () use (
                    $freezer,
                    $data,
                    $newPhotoPath,
                ): Freezer {
                    $updateData = Arr::except(
                        $data,
                        ['photo'],
                    );

                    if ($newPhotoPath !== null) {
                        $updateData['photo_path'] = $newPhotoPath;
                    }

                    $freezer->update($updateData);

                    return $freezer->refresh();
                },
            );
        } catch (Throwable $exception) {
            if ($newPhotoPath !== null) {
                Storage::disk('public')->delete(
                    $newPhotoPath,
                );
            }

            throw $exception;
        }

        if (
            $newPhotoPath !== null &&
            $oldPhotoPath !== null &&
            $oldPhotoPath !== $newPhotoPath
        ) {
            Storage::disk('public')->delete(
                $oldPhotoPath,
            );
        }

        return $updatedFreezer;
    }
}
