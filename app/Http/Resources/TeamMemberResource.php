<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'bio' => $this->bio,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'email' => $this->email,
            'linkedin_url' => $this->linkedin_url,
            'github_url' => $this->github_url,
        ];
    }
}
