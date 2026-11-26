<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectCard;
use App\Models\Project;
use App\Models\ProjectType;
use App\Support\JsonLd;
use App\Support\Seo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $type = $request->string('loai')->toString() ?: null;

        $projects = Project::published()
            ->with(['media', 'type'])
            ->when($type, fn ($query) => $query->whereHas('type', fn ($q) => $q->where('slug', $type)))
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Projects/Index', [
            'seo' => Seo::make('Dự án cây xanh đã thực hiện', 'Các công trình cảnh quan, cung cấp và chăm sóc cây xanh tiêu biểu.', jsonLd: [
                JsonLd::breadcrumbs(['Dự án' => null]),
            ]),
            'projects' => ProjectCard::collection($projects)->response()->getData(true),
            'types' => ProjectType::query()->whereHas('projects', fn ($q) => $q->published())->orderBy('sort')->get(['name', 'slug']),
            'filters' => ['loai' => $type],
        ]);
    }

    public function show(string $slug): Response
    {
        $project = Project::published()->where('slug', $slug)->with(['media', 'type', 'services'])->firstOrFail();

        return Inertia::render('Projects/Show', [
            'seo' => Seo::make($project->seoTitle(), $project->seoDescription(), $project->imageUrls('cover')['large'] ?? null, [
                JsonLd::breadcrumbs(['Dự án' => '/du-an', $project->name => null]),
            ], 'article'),
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'type' => $project->type?->only(['name', 'slug']),
                'client' => $project->client,
                'location' => $project->location,
                'year' => $project->year,
                'scale' => $project->scale,
                'summary' => $project->summary,
                'body' => $project->body,
                'videoUrl' => $project->video_url,
                'image' => $project->imageUrls('cover'),
                'gallery' => $project->galleryUrls(),
                'services' => $project->services->map(fn ($service) => ['name' => $service->name, 'url' => $service->publicPath()])->all(),
            ],
            'related' => ProjectCard::collection(
                Project::published()->whereKeyNot($project->id)->where('project_type_id', $project->project_type_id)->with(['media', 'type'])->latest()->limit(3)->get()
            )->resolve(),
        ]);
    }
}
