<?php

namespace Masroore\SocialAuth\Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Masroore\SocialAuth\Services\ImageProcessor;

trait SetsProfilePhotoFromUrl
{
    /**
     * Sets the users profile photo from a URL.
     */
    public function setProfilePhotoFromUrl(string $url): void
    {
        $response = Http::get($url);

        if ($response->successful()) {
            [$origName, $ext] = $this->getFilenameFromUrl($url);
            $tmpFilename = self::tempFilePath('profile_', $ext);

            if (@file_put_contents($tmpFilename, $response) !== false) {
                ImageProcessor::resizeImage($tmpFilename);

                $this->updateProfilePhoto(new UploadedFile($tmpFilename, $origName));

                @unlink($tmpFilename);
            }
        }
    }

    /**
     * @return array<string>
     */
    private function getFilenameFromUrl(string $url): array
    {
        $filename = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_BASENAME);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (blank($ext)) {
            $ext = 'jpg';
        }

        return [$filename, $ext];
    }

    private static function tempFilePath(string $prefix, string $extension): string
    {
        while (true) {
            $path = sys_get_temp_dir() . '/' . uniqid(sprintf('%s%d', $prefix, mt_rand()), true) . '.' . $extension;
            if (!file_exists($path)) {
                return $path;
            }
        }
    }
}
