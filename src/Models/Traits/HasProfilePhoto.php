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
        $profile_photo = $this->getProfilePhotoPath();

        tap($profile_photo, function ($previous) use ($photo, $storagePath): void {
            $this->forceFill([
                $this->getProfilePhotoColumnName() => $photo->storePublicly($storagePath, ['disk' => $this->profilePhotoDisk()]),
            ])->save();

            if ($previous) {
                Storage::disk($this->profilePhotoDisk())->delete($previous);
            }
        });
    }

    public function getProfilePhotoPath(): ?string
    {
        $attribute_name = $this->getProfilePhotoColumnName();

        if (array_key_exists($attribute_name, $this->getAttributes())) {
            return $this->getAttributes()[$attribute_name];
        }

        return null;
    }

    public function getProfilePhotoColumnName(): string
    {
        return sa_config('columns.profile_photo_path', 'profile_photo_path');
    }

    /**
     * Get the disk that profile photos should be stored on.
     */
    protected function profilePhotoDisk(): string
    {
        return (string) sa_config('profile_photo.disk', 'public');
    }

    public function deleteProfilePhoto(): void
    {
        if (!Features::managesProfilePhotos()) {
            return;
        }
        $photo = $this->getProfilePhotoPath();
        if (null === $photo) {
            return;
        }

        Storage::disk($this->profilePhotoDisk())->delete($photo);

        $this->forceFill([$this->getProfilePhotoColumnName() => null])->save();
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function profilePhotoUrl(): Attribute
    {
        $profile_photo_path = $this->getProfilePhotoPath();

        return Attribute::get(fn (): string => $profile_photo_path
            ? Storage::disk($this->profilePhotoDisk())->url($profile_photo_path)
            : $this->defaultProfilePhotoUrl());
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     */
    protected function defaultProfilePhotoUrl(): string
    {
        $name = $this->getAttributes()[sa_config('columns.name', 'name')];
        $nameInitials = trim(collect(explode(' ', $name))->map(static fn ($segment): string => mb_substr($segment, 0, 1))->join(' '));
        $url_params = [
            'name' => $nameInitials,
            'color' => sa_config('profile_photo.color', '7F9CF5'),
            'background' => sa_config('profile_photo.background', 'EBF4FF'),
            'length' => sa_config('profile_photo.length', 2),
            'size' => sa_config('profile_photo.dimensions', 128),
        ];

        return 'https://ui-avatars.com/api/?' . http_build_query($url_params);
    }

    protected function profilePhotoSize(): int
    {
        return (int) sa_config('profile_photo.dimensions', 180);
    }
}
