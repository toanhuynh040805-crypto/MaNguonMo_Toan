<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'stock', 'image_path', 'is_active',
    ];

    protected function casts(): array
    {
        return ['price' => 'integer', 'stock' => 'integer', 'is_active' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        // An uploaded product photo always takes precedence over the bundled
        // demo illustration. Do not overwrite saved local images on upgrade.
        if ($this->image_path) {
            return Storage::disk('public')->url($this->image_path);
        }

        // Real photos are actual Wikimedia Commons photographs, not AI illustrations.
        // Local optimized images (if downloaded) take priority over remote URLs.
        $slug = $this->slug;
        $photo = \App\Support\RealProductPhotos::forSlug($slug);
        if ($photo) {
            return \App\Support\RealProductPhotos::localUrl($slug)
                ?? \App\Support\RealProductPhotos::remoteUrl($photo);
        }

        return null;
    }
}
