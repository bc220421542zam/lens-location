<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoStorage
{
    private const DISK = 'public';
    private const FOLDER = 'profiles';

    public static function replace(User $user, UploadedFile $photo): string
    {
        if ($user->profile_picture) {
            Storage::disk(self::DISK)->delete($user->profile_picture);
        }

        return $photo->store(self::FOLDER, self::DISK);
    }
}
