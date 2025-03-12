<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'position', 'bio', 'image', 'email', 'linkedin_url', 'github_url', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
    ];


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
