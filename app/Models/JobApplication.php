<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = ['job_id', 'full_name', 'email', 'phone', 'cover_letter', 'resume_path', 'status'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    protected function createdAtFormatted(): Attribute
    {
        return Attribute::make(get: fn($value, $attributes) => $attributes['created_at']->format('H:i d, M Y'));
    }

    protected function updatedAtFormatted(): Attribute
    {
        return Attribute::make(get: fn($value, $attributes) => $attributes['updated_at']->format('H:i d, M Y'));
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeExpired($query)
    {
        return $query->where('deadline', '<', now());
    }


}
