<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostCard;
use App\Models\Post;
use App\Models\PostCategory;
use App\Settings\GeneralSettings;
use App\Support\JsonLd;
use App\Support\Seo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->listing(null, $request);
    }

    public function category(string $slug, Request $request): Response
    {
        return $this->listing(PostCategory::where('slug', $slug)->firstOrFail(), $request);
    }

    public function show(string $slug, GeneralSettings $settings): Response
    {
        $post = Post::published()->where('slug', $slug)->with(['media', 'category', 'tags'])->firstOrFail();

        return Inertia::render('Posts/Show', [
            'seo' => Seo::make($post->seoTitle(), $post->seoDescription(), $post->imageUrls('cover')['large'] ?? null, [
                JsonLd::article($post, $settings),
                JsonLd::breadcrumbs(['Tin tức' => '/tin-tuc', $post->title => null]),
            ], 'article'),
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'excerpt' => $post->excerpt,
                'body' => $post->body,
                'date' => $post->published_at?->format('d/m/Y'),
                'image' => $post->imageUrls('cover'),
                'category' => $post->category ? ['name' => $post->category->name, 'url' => '/tin-tuc/danh-muc/'.$post->category->slug] : null,
                'tags' => $post->tags->pluck('name')->all(),
            ],
            'related' => PostCard::collection(
                Post::published()->whereKeyNot($post->id)
                    ->when($post->post_category_id, fn ($query) => $query->where('post_category_id', $post->post_category_id))
                    ->with(['media', 'category'])->latest('published_at')->limit(3)->get()
            )->resolve(),
            'categories' => $this->categories(),
        ]);
    }

    private function listing(?PostCategory $category, Request $request): Response
    {
        $search = trim($request->string('q')->toString());

        $posts = Post::published()
            ->with(['media', 'category'])
            ->when($category, fn ($query) => $query->where('post_category_id', $category->id))
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%'.$search.'%'))
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $title = $category ? $category->name : 'Tin tức & kiến thức cây xanh';

        return Inertia::render('Posts/Index', [
            'seo' => Seo::make($title, 'Tin tức, kinh nghiệm chọn và chăm sóc cây xanh, cây cảnh nghệ thuật.', jsonLd: [
                JsonLd::breadcrumbs($category ? ['Tin tức' => '/tin-tuc', $category->name => null] : ['Tin tức' => null]),
            ]),
            'posts' => PostCard::collection($posts)->response()->getData(true),
            'category' => $category?->only(['name', 'slug']),
            'categories' => $this->categories(),
            'filters' => ['q' => $search ?: null],
        ]);
    }

    /** @return array<int, array{name: string, url: string, count: int}> */
    private function categories(): array
    {
        return PostCategory::query()->withCount(['posts' => fn ($query) => $query->published()])->orderBy('sort')->get()
            ->filter(fn (PostCategory $category) => $category->posts_count > 0)
            ->map(fn (PostCategory $category) => ['name' => $category->name, 'url' => '/tin-tuc/danh-muc/'.$category->slug, 'count' => $category->posts_count])
            ->values()->all();
    }
}
