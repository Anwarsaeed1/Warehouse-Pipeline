<?php

namespace App\Services\Global;

use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadService
{
    /**
     * @param $files
     * @param string $path
     * @param string $extension
     * @param string $disk
     * @return array|false|mixed|string
     */
    public static function store($files = null, string $path = 'files', string $extension = 'png', string $disk = 'public'): mixed
    {
        $items = is_array($files) ? $files : [$files];

        $paths = [];
        foreach (array_filter($items) as $item) {

            if (is_string($item) && ($data = explode(',', $item))) {
                try {
                    $base64_content = $data[1] ?? $data[0];
                    $decoded_content = base64_decode($base64_content);
                    $file = $path . '/' . (isset($data[1]) ? self::generateUniqueFileName($item) : (time() . ".$extension"));

                    $paths[] = Storage::disk($disk)->put($file, $decoded_content) ? $file : null;

                } catch (Exception $e) {
                    logError($e);
                }

            } else {
                $paths[] = is_file($item) ? Storage::disk($disk)->putFile($path, $item) : null;
            }
        }

        $paths = array_filter($paths);

        return count($paths) > 1 ? $paths : ($paths[0] ?? null);
    }

    public static function delete(array|string|null $files = null, string $disk = 'public'): bool
    {
        $items = is_array($files) ? $files : [$files];

        foreach ($items as $item) {
            if (!empty($item) && Storage::disk($disk)->exists($item)) {
                Storage::disk($disk)->delete($item);
            }
        }

        return true;
    }

    /**
     * @param string|null $path
     * @return string|null
     */
    public static function url(?string $path = null): ?string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return $path && Storage::exists($path) ? Storage::url($path) : null;
    }

    /**
     * @param string $originalFileName
     * @return string
     */
    public static function generateUniqueFileName(string $originalFileName): string
    {
        $extensionMap = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/octet-stream' => 'docx', // or 'xlsx' based on your needs
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'text/plain' => 'txt',
        ];

        $extension = $extensionMap[mime_content_type($originalFileName)] ?? pathinfo($originalFileName, PATHINFO_EXTENSION);

        return time() . '_' . Str::random(8) . '.' . $extension;
    }

}
