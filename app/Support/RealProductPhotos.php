<?php

namespace App\Support;

class RealProductPhotos
{
    public static function all(): array
    {
        static $items = null;
        if ($items === null) {
            $path = resource_path('data/real_product_photos.json');
            $items = is_file($path) ? json_decode(file_get_contents($path), true) : [];
        }
        return $items;
    }

    public static function forSlug(string $slug): ?array
    {
        foreach (self::all() as $photo) {
            if ($photo['slug'] === $slug) {
                return $photo;
            }
        }
        return null;
    }

    public static function remoteUrl(array $photo): string
    {
        return 'https://commons.wikimedia.org/wiki/Special:Redirect/file/'
            . rawurlencode($photo['file']) . '?width=960';
    }

    public static function localUrl(string $slug): ?string
    {
        // Images are stored as optimized real photographs by the download script.
        $relative = 'images/real-products/' . $slug . '.webp';
        return is_file(public_path($relative)) ? asset($relative) : null;
    }
}
