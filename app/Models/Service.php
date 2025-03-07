<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    protected $fillable = ['title', 'slug', 'description', 'features', 'icon', 'image', 'is_active', 'sort_order'];

    // Auto-generate slug from title
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (!$service->slug) {
                $service->slug = Str::slug($service->title);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getTitleAttribute($value)
    {
        return ucfirst($value);
    }

    public function getDescriptionAttribute($value)
    {
        return ucfirst($value);
    }

    public function getFeaturesAttribute($value)
    {
        return ucfirst($value);
    }

    public function getIconAttribute($value)
    {
        return ucfirst($value);
    }

    public function getImageAttribute($value)
    {
        return ucfirst($value);
    }
}
