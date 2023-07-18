<?php

namespace Masroore\SocialAuth\Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Masroore\SocialAuth\Support\Features;

trait SetsProfilePhotoFromUrl
{
    /**
     * Sets the users profile photo from a URL.
     */
    public function setProfilePhotoFromUrl(string $url): void
    {
        $origName = pathinfo($url)['basename'];
        $response = Http::get($url);

        // Determine if the status code is >= 200 and < 300
        if ($response->successful()) {
            $path = self::tempFilePath();

            if (@file_put_contents($path, $response) !== false) {
                $this->resizeImage($path);

                $this->updateProfilePhoto(new UploadedFile($path, $origName));

                @unlink($path);
            }
        }
    }

    private static function tempFilePath(): string
    {
        while (true) {
            $path = tempnam(sys_get_temp_dir(), uniqid(mt_rand(), true));
            if (!file_exists($path)) {
                return $path;
            }
        }
    }

    private function resizeImage(string $path): void
    {
        $size = $this->profilePhotoDimensions();

        if (!Features::resizeProfilePhoto() || $size < 24) {
            return;
        }

        $img = Image::make($path);
        if ($img->height() > $size || $img->width() > $size) {
            $img->resize($size, $size, fn ($cons) => $cons->aspectRatio());
            $img->save();
        }
    }

    protected function profilePhotoDimensions(): int
    {
        return (int) sa_config('profile_photo.dimensions', 180);
    }
}
