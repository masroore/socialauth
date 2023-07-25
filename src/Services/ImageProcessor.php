<?php

namespace Masroore\SocialAuth\Services;

use Intervention\Image\Facades\Image;
use Masroore\SocialAuth\Support\Features;

final class ImageProcessor
{
    public static function resizeImage(string $path): void
    {
        $size = self::profilePhotoDimensions();
        $quality = self::profilePhotoQuality();

        if (!Features::resizeProfilePhoto() || $size < 24) {
            return;
        }

        $img = Image::make($path);
        if ($img->getHeight() > $size || $img->getWidth() > $size) {
            $img->resize($size, $size, static fn ($cons) => $cons->aspectRatio());
            $img->save(quality: $quality);
        }
    }

    private static function profilePhotoDimensions(): int
    {
        return (int) sa_config('profile_photo.dimensions', 180);
    }

    private static function profilePhotoQuality(): int
    {
        return (int) sa_config('profile_photo.quality', 70);
    }
}
