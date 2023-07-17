<?php

namespace Masroore\SocialAuth\Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
            file_put_contents($path = sys_get_temp_dir() . '/' . Str::uuid()->toString(), $response);

            $this->updateProfilePhoto(new UploadedFile($path, $origName));
        } else {
            flash()->warning('Unable to retrieve profile image');
        }
    }
}
