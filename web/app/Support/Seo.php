<?php

namespace App\Support;

use App\Settings\GeneralSettings;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

/**
 * @implements Arrayable<string, mixed>
 */
class Seo implements Arrayable
{
    /**
     * @param  array<int, array<string, mixed>>  $jsonLd
     */
    public function __construct(
        public string $title,
        public string $description = '',
        public ?string $canonical = null,
        public ?string $image = null,
        public array $jsonLd = [],
        public string $type = 'website',
    ) {}

    /**
     * @param  array<int, array<string, mixed>>  $jsonLd
     */
    public static function make(string $title, string $description = '', ?string $image = null, array $jsonLd = [], string $type = 'website'): self
    {
        return new self($title, $description, url()->current(), $image, $jsonLd, $type);
    }

    /**
     * The page title with the brand appended, unless the title already names it.
     */
    public function fullTitle(): string
    {
        $siteName = app(GeneralSettings::class)->site_name;

        if (blank($siteName) || Str::contains($this->title, $siteName)) {
            return $this->title;
        }

        return $this->title.' | '.$siteName;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->fullTitle(),
            'description' => $this->description,
            'canonical' => $this->canonical,
            'image' => $this->image,
            'type' => $this->type,
            'jsonLd' => array_values($this->jsonLd),
        ];
    }
}
