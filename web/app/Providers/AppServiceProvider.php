<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Slide;
use App\Models\Testimonial;
use App\Observers\FlushSiteCache;
use App\Services\Cart;
use App\Support\SiteCache;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelSettings\Events\SettingsSaved;
use Spatie\MediaLibrary\Conversions\Events\ConversionHasBeenCompletedEvent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(Cart::class, fn ($app) => new Cart($app['request']->session()));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ([
            Category::class, Faq::class, Page::class, Partner::class, Post::class, PostCategory::class, Product::class,
            Project::class, ProjectType::class, Service::class, ServicePackage::class, Slide::class, Testimonial::class, Media::class,
        ] as $model) {
            $model::observe(FlushSiteCache::class);
        }

        Event::listen([SettingsSaved::class, ConversionHasBeenCompletedEvent::class], fn () => SiteCache::flush());

        Relation::morphMap([
            'product' => Product::class,
            'service' => Service::class,
            'package' => ServicePackage::class,
        ]);
    }
}
