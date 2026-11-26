<?php

namespace App\Http\Resources;

use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Slide
 */
class SlideData extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'buttonText' => $this->button_text,
            'buttonUrl' => $this->button_url,
            'image' => $this->imageUrls('image'),
        ];
    }
}
