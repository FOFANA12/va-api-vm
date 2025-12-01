<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileHelper
{
    /**
     * URL avatar (wrapper de url()).
     */
    public static function avatarUrl(?string $path, int $expiration = 5): ?string
    {
        return static::url($path, $expiration);
    }

    // ──────────── UPLOAD & DELETE ────────────

    public static function upload(UploadedFile $file, string $directory = 'uploads', ?string $name = null): string
    {
        $filename = ($name ?? Str::uuid()) . '.' . $file->getClientOriginalExtension();
        $disk = config('filesystems.default', 'public');

        Storage::disk($disk)->putFileAs($directory, $file, $filename);

        return $filename;
    }

    public static function uploadLocal(UploadedFile $file, string $directory = 'uploads', ?string $name = null): string
    {
        $filename = ($name ?? Str::uuid()) . '.' . $file->getClientOriginalExtension();

        Storage::disk('local')->putFileAs($directory, $file, $filename);

        return $filename;
    }

    public static function delete(?string $path): bool
    {
        if (blank($path)) return false;

        $disk = Storage::disk(config('filesystems.default', 'public'));
        return $disk->exists($path) ? $disk->delete($path) : false;
    }

    // ──────────── URL ────────────

    public static function url(?string $path, int $expiration = 5): ?string
    {
        if (blank($path)) return null;

        $disk = Storage::disk(config('filesystems.default', 'public'));
        if (! $disk->exists($path)) return null;

        return method_exists($disk, 'temporaryUrl')
            ? $disk->temporaryUrl($path, now()->addMinutes($expiration))
            : $disk->url($path);
    }

    // ──────────── DOWNLOAD ────────────

    public static function download(string $path, ?string $name = null, int $expiration = 5)
    {
        $disk = Storage::disk(config('filesystems.default', 'public'));
        if (! $disk->exists($path)) return null;

        if (method_exists($disk, 'temporaryUrl')) {
            $url = $disk->temporaryUrl($path, now()->addMinutes($expiration), [
                'ResponseContentDisposition' => 'attachment; filename="' . ($name ?? basename($path)) . '"'
            ]);
            return redirect()->away($url);
        }

        return $disk->download($path, $name);
    }
}
