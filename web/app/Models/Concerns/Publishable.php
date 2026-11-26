<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait Publishable
{
    public function scopePublished(Builder $query): void
    {
        $query->where($this->qualifyColumn('is_published'), true);

        if (in_array('published_at', $this->getFillable(), true)) {
            $query->where(fn (Builder $q) => $q->whereNull($this->qualifyColumn('published_at'))
                ->orWhere($this->qualifyColumn('published_at'), '<=', now()));
        }
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy($this->qualifyColumn('sort'))->orderBy($this->getQualifiedKeyName());
    }
}
