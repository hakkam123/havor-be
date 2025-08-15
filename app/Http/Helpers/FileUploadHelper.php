<?php

namespace App\Http\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadHelper
{
    public static function uploadImage(UploadedFile $file, string $directory = null): string
    {
        $directory = $directory ?? env('APP_IMAGE_PATH', 'app/image');
        
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        $path = $file->storeAs($directory, $filename, 'public');
        
        // Return only relative path, not full URL
        return $path;
    }

    public static function deleteImage(string $path): bool
    {
        if (empty($path)) {
            return true;
        }

        // If path already contains full URL, extract relative path
        if (str_contains($path, '/storage/')) {
            $path = str_replace(env('APP_URL') . '/storage/', '', $path);
            $path = str_replace('/storage/', '', $path);
        }
        
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return true;
    }

    public static function validateImageFile(UploadedFile $file): array
    {
        $maxSize = env('APP_UPLOAD_MAX_SIZE', 2048) * 1024; 
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];

        $errors = [];

        if ($file->getSize() > $maxSize) {
            $errors[] = 'File size must be less than ' . (env('APP_UPLOAD_MAX_SIZE', 2048) / 1024) . 'MB';
        }

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'File must be an image (JPEG, PNG, GIF, WebP, SVG)';
        }

        return $errors;
    }
}
