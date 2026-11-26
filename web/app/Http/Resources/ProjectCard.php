<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Project
 */
class ProjectCard extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'url' => $this->publicPath(),
            'type' => $this->type?->only(['name', 'slug']),
            'location' => $this->location,
            'year' => $this->year,
            'summary' => $this->summary,
            'image' => $this->imageUrls('cover') ?? ($this->galleryUrls()[0] ?? null),
        ];
    }
}
