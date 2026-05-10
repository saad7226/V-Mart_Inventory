<?php

namespace App\Trait;

use Illuminate\Support\Facades\Storage;

class FileHandler
{
    /**
     * Upload a file to public/uploads/ (InfinityFree shared hosting compatible).
     *
     * Saves directly to public_path() — no storage:link symlink needed.
     * Returns a relative path like "uploads/images/filename.jpg" suitable for asset().
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $folder   Sub-folder inside public/uploads/ (e.g. "media/products")
     * @return string  Relative path stored in DB (e.g. "uploads/media/products/file.jpg")
     */
    public function fileUploadAndGetPath($file, string $folder = 'media/others'): string
    {
        // Normalize: strip leading slashes and "public/" prefix for uniformity
        $folder = ltrim(str_replace('/public/', '', $folder), '/');

        $fileName    = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $destination = public_path('uploads/' . $folder);

        // Ensure destination directory exists
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        // Move the file directly into public/uploads/...
        $file->move($destination, $fileName);

        return 'uploads/' . $folder . '/' . $fileName;
    }

    /**
     * Upload and optionally resize an image using Intervention Image.
     * Falls back to plain move() if Intervention is unavailable.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $folder  Sub-folder inside public/uploads/
     * @param  int|null  $width
     * @param  int|null  $height
     * @return string  Relative path (e.g. "uploads/media/users/file.jpg")
     */
    public function uploader($file, string $folder = '/public/media/others', ?int $width = null, ?int $height = null): string
    {
        return $this->fileUploadAndGetPath($file, $folder);
    }

    /**
     * Upload to public assets (legacy wrapper — kept for backward compatibility).
     */
    public function uploadToPublic($file, string $path = '/assets/images'): string
    {
        return $this->fileUploadAndGetPath($file, ltrim($path, '/'));
    }

    /**
     * Delete a file stored in public/uploads/ by its relative path.
     *
     * @param  string|null  $relativePath  e.g. "uploads/media/users/file.jpg"
     */
    public function secureUnlink(?string $relativePath): bool
    {
        if (empty($relativePath)) {
            return false;
        }

        // Support old storage/ paths — silently skip them (can't delete, just ignore)
        if (str_starts_with($relativePath, 'media/') || str_starts_with($relativePath, '/media/')) {
            return false; // Old storage-based path — not accessible on InfinityFree
        }

        $absolutePath = public_path($relativePath);

        if (file_exists($absolutePath) && is_file($absolutePath)) {
            unlink($absolutePath);
            return true;
        }

        return false;
    }

    /**
     * Delete a file directly from the public path (legacy method).
     */
    public function securePublicUnlink(?string $relativePath): bool
    {
        return $this->secureUnlink($relativePath);
    }
}
