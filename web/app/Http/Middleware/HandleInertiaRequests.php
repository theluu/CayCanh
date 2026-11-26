<?php

namespace App\Http\Middleware;

use App\Models\Service;
use App\Services\Cart;
use App\Settings\GeneralSettings;
use App\Support\SiteCache;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'site' => fn () => SiteCache::remember('settings.public', fn () => app(GeneralSettings::class)->publicData()),
            'menu' => fn () => SiteCache::remember('menu', fn () => [
                'services' => Service::published()->ordered()->get(['name', 'slug'])
                    ->map(fn (Service $service) => ['name' => $service->name, 'url' => $service->publicPath()])
                    ->all(),
            ]),
            'cart' => fn () => ['count' => $request->hasSession() ? app(Cart::class)->count() : 0],
            'flash' => fn () => [
                'success' => $request->hasSession() ? $request->session()->get('success') : null,
                'error' => $request->hasSession() ? $request->session()->get('error') : null,
            ],
        ];
    }
}
