<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasSeo
{
    public function seoTitle(): string
    {
        return $this->seo_title ?: (string) $this->{$this->slugSourceColumn()};
    }

    public function seoDescription(): string
    {
        $fallback = $this->summary ?? $this->excerpt ?? strip_tags((string) ($this->description ?? $this->body ?? ''));

        return $this->seo_description ?: Str::limit(trim(preg_replace('/\s+/', ' ', (string) $fallback)), 160);
    }
}
