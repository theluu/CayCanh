<?php

namespace App\Models\Concerns;

use App\Models\Redirect;
use Illuminate\Support\Str;

/**
 * Generates a unique slug from the source column and records a 301 redirect
 * when the slug of a publicly routed model changes.
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::saving(function (self $model) {
            $base = Str::slug(filled($model->slug) ? $model->slug : (string) $model->{$model->slugSourceColumn()});

            if (blank($model->slug) || $model->isDirty('slug')) {
                $model->slug = $model->uniqueSlug($base ?: Str::lower(Str::random(6)));
            }
        });

        static::updated(function (self $model) {
            $prefix = $model->publicPathPrefix();

            if ($prefix !== null && $model->wasChanged('slug') && filled($model->getRawOriginal('slug'))) {
                Redirect::record($prefix.'/'.$model->getRawOriginal('slug'), $prefix.'/'.$model->slug);
            }
        });
    }

    public function slugSourceColumn(): string
    {
        return 'name';
    }

    /** The public URL prefix for this model, or null when it has no public page. */
    public function publicPathPrefix(): ?string
    {
        return null;
    }

    public function publicPath(): ?string
    {
        $prefix = $this->publicPathPrefix();

        return $prefix === null ? null : $prefix.'/'.$this->slug;
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base;
        $suffix = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($this->exists, fn ($query) => $query->whereKeyNot($this->getKey()))
            ->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
