<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'client_name' => $this->client_name,
            'service' => [
                'id' => $this->service->id,
                'title' => $this->service->title,
                'slug' => $this->service->slug,
                'icon' => $this->service->icon_url,
            ],
            'completion_date' => $this->completion_date,
            'technologies' => $this->technologies,
            'website_url' => $this->website_url,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'gallery' => $this->gallery ? collect($this->gallery)->map(fn($image) => asset('storage/' . $image)) : [],
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 