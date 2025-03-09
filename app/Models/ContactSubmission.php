<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'company', 'subject', 'message', 'status', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function markAsRead()
    {
        if (!$this->read_at) {
            $this->update([
                'read_at' => now(),
                'status' => $this->status === 'new' ? 'read' : $this->status,
            ]);
        }
    }
}
