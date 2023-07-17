<?php

namespace Masroore\SocialAuth\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Masroore\SocialAuth\Services\Features;
use Masroore\SocialAuth\SocialAuth;

trait HasProfilePhoto
{
    public function updateProfilePhoto(UploadedFile $photo, string $storagePath = 'profile-photos'): void
    {
        tap($this->profile_photo, function ($previous) use ($photo, $storagePath): void {
            $this->forceFill([
                'profile_photo' => $photo->storePublicly($storagePath, ['disk' => $this->profilePhotoDisk()]),
            ])->save();

            if ($previous) {
                Storage::disk($this->profilePhotoDisk())->delete($previous);
            }
        });
    }

    public function deleteProfilePhoto(): void
    {
        if (!Features::profilePhoto()) {
            return;
        }

        if (null === $this->profile_photo) {
            return;
        }

        Storage::disk($this->profilePhotoDisk())->delete($this->profile_photo);

        $this->forceFill([
            'profile_photo' => null,
        ])->save();
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->profile_photo
                ? Storage::disk($this->profilePhotoDisk())->url($this->profile_photo)
                : $this->defaultProfilePhotoUrl();
        });
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     */
    protected function defaultProfilePhotoUrl(): string
    {
        $nameInitials = trim(collect(explode(' ', $this->name))->map(fn ($segment) => mb_substr($segment, 0, 1))->join(' '));

        return 'https://ui-avatars.com/api/?name=' . urlencode($nameInitials) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the disk that profile photos should be stored on.
     */
    protected function profilePhotoDisk(): string
    {
        return config(SocialAuth::PACKAGE_NAME . '.profile_photo_disk', 'public');
    }
}
