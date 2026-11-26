<?php

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class PostCard extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => $this->publicPath(),
            'excerpt' => $this->excerpt,
            'date' => $this->published_at?->format('d/m/Y'),
            'category' => $this->category?->only(['name', 'slug']),
            'image' => $this->imageUrls('cover'),
        ];
    }
}
