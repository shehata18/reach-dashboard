<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'features' => $this->features,
            'icon' => $this->icon_url,
            'image' => $this->image_url,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'projects_count' => $this->when($this->projects_count, $this->projects_count),
            'projects' => ProjectResource::collection($this->whenLoaded('projects')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
