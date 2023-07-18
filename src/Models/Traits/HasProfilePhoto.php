<?php

namespace Masroore\SocialAuth\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Masroore\SocialAuth\Support\Features;

trait HasProfilePhoto
{
    public function updateProfilePhoto(UploadedFile $photo, string $storagePath = 'profile-photos'): void
    {
        $attribute_name = sa_config('columns.profile_photo', 'profile_photo');
        $profile_photo = $this->getProfilePhoto();

        tap($profile_photo, function ($previous) use ($attribute_name, $photo, $storagePath): void {
            $this->forceFill([
                $attribute_name => $photo->storePublicly($storagePath, ['disk' => $this->profilePhotoDisk()]),
            ])->save();

            if ($previous) {
                Storage::disk($this->profilePhotoDisk())->delete($previous);
            }
        });
    }

    public function getProfilePhoto(): ?string
    {
        $attribute_name = sa_config('columns.profile_photo', 'profile_photo');

        if (array_key_exists($attribute_name, $this->getAttributes())) {
            return $this->getAttributes()[$attribute_name];
        }

        return null;
    }

    public function deleteProfilePhoto(): void
    {
        if (!Features::profilePhoto()) {
            return;
        }
        $photo = $this->getProfilePhoto();
        if (null === $photo) {
            return;
        }

        Storage::disk($this->profilePhotoDisk())->delete($photo);

        $this->forceFill([
            sa_config('columns.profile_photo', 'profile_photo') => null,
        ])->save();
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function profilePhotoUrl(): Attribute
    {
        $photo = $this->getProfilePhoto();

        return Attribute::get(function () use ($photo) {
            return $photo
                ? Storage::disk($this->profilePhotoDisk())->url($photo)
                : $this->defaultProfilePhotoUrl();
        });
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     */
    protected function defaultProfilePhotoUrl(): string
    {
        $attribute = sa_config('columns.name', 'name');
        $name = $this->getAttributes()[$attribute];
        $nameInitials = trim(collect(explode(' ', $name))->map(fn ($segment) => mb_substr($segment, 0, 1))->join(' '));

        return 'https://ui-avatars.com/api/?name=' . urlencode($nameInitials) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the disk that profile photos should be stored on.
     */
    protected function profilePhotoDisk(): string
    {
        return (string) sa_config('profile_photo.disk', 'public');
    }

    protected function profilePhotoSize(): int
    {
        return (int) sa_config('profile_photo.size', 180);
    }
}
