<?php

namespace App\Models\Concerns;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Shared WebP conversions (thumb 400, medium 800, large 1600) for every image collection.
 */
trait HasImageConversions
{
    use InteractsWithMedia;

    public function registerMediaConversions(?Media $media = null): void
    {
        foreach (['thumb' => 400, 'medium' => 800, 'large' => 1600] as $name => $width) {
            $this->addMediaConversion($name)
                ->fit(Fit::Max, $width, $width)
                ->format('webp')
                ->quality(80)
                ->withResponsiveImages()
                ->queued();
        }
    }

    /** @return array{thumb: string, medium: string, large: string, original: string}|null */
    public function imageUrls(string $collection = 'cover'): ?array
    {
        $media = $this->getFirstMedia($collection);

        return $media ? static::mediaUrls($media) : null;
    }

    /** @return array<int, array{thumb: string, medium: string, large: string, original: string}> */
    public function galleryUrls(string $collection = 'gallery'): array
    {
        return $this->getMedia($collection)->map(fn (Media $media) => static::mediaUrls($media))->values()->all();
    }

    /** @return array{thumb: string, medium: string, large: string, original: string, alt: string} */
    public static function mediaUrls(Media $media): array
    {
        $url = fn (string $conversion) => $media->hasGeneratedConversion($conversion) ? $media->getUrl($conversion) : $media->getUrl();

        return [
            'thumb' => $url('thumb'),
            'medium' => $url('medium'),
            'large' => $url('large'),
            'original' => $media->getUrl(),
            'alt' => (string) ($media->getCustomProperty('alt') ?: $media->name),
        ];
    }
}
