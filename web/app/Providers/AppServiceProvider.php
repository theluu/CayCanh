<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Services\Cart;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

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
        Relation::morphMap([
            'product' => Product::class,
            'service' => Service::class,
            'package' => ServicePackage::class,
        ]);
    }
}
