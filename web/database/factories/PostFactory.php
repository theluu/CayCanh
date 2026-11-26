<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'post_category_id' => PostCategory::factory(),
            'title' => fake()->unique()->sentence(6),
            'excerpt' => fake()->sentence(20),
            'body' => '<p>'.fake()->paragraphs(4, true).'</p>',
            'is_published' => true,
            'published_at' => now()->subDays(fake()->numberBetween(1, 60)),
        ];
    }
}
