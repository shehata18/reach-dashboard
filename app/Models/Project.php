<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Project extends Model
{
    use HasFactory;

    protected $casts = [
        'technologies' => 'array',
        'gallery' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'completion_date' => 'date',
    ];

    protected $fillable = ['title', 'slug', 'description', 'client_name', 'service_id', 'completion_date', 'technologies', 'website_url', 'image', 'gallery', 'is_featured', 'is_active', 'sort_order'];

    // Relationship with Service
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (!$project->slug) {
                $project->slug = Str::slug($project->title);
            }
        });
    }

      protected function createdAtFormatted(): Attribute
    {
        return Attribute::make(get: fn($value, $attributes) => $attributes['created_at']->format('H:i d, M Y'));
    }

    protected function updatedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['updated_at']->format('H:i d, M Y'),
        );
    }
}
