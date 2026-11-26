<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Database\Factories\ProjectTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'sort'])]
class ProjectType extends Model
{
    /** @use HasFactory<ProjectTypeFactory> */
    use HasFactory, HasSlug;

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
