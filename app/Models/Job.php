<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
class Job extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'description', 'location', 'type', 'experience_level', 'salary_min', 'salary_max', 'department', 'is_active', 'deadline', 'positions_available', 'required_skills'];

    protected $casts = [
        'required_skills' => 'array',
        'is_active' => 'boolean',
        'deadline' => 'date',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = \Str::slug($value);
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where('deadline', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('deadline', '<', now());
    }

    public function scopePositionsAvailable($query)
    {
        return $query->where('positions_available', '>', 0);
    }

    public function scopePositionsFilled($query)
    {
        return $query->where('positions_available', 0);
    }

    protected function deadlineFormatted(): Attribute
    {
        return Attribute::make(get: fn($value, $attributes) => $attributes['deadline']->format('H:i d, M Y'));
    }

    protected function createdAtFormatted(): Attribute
    {
        return Attribute::make(get: fn($value, $attributes) => $attributes['created_at']->format('H:i d, M Y'));
    }

    protected function updatedAtFormatted(): Attribute
    {
        return Attribute::make(get: fn($value, $attributes) => $attributes['updated_at']->format('H:i d, M Y'));
    }




}
