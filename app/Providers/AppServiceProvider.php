<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Destination;
use App\Observers\CategoryObserver;
use App\Observers\DestinationObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Destination::observe(DestinationObserver::class);
        Category::observe(CategoryObserver::class);

        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('public-api', function (Request $request): Limit {
            if ($request->user()) {
                return Limit::perMinute(300)->by('public-api:user:' . $request->user()->getKey());
            }

            return Limit::perMinute(120)->by('public-api:ip:' . $request->ip());
        });

        RateLimiter::for('auth', function (Request $request): Limit {
            return Limit::perMinute(5)->by('auth:ip:' . $request->ip());
        });

        RateLimiter::for('auth-api', function (Request $request): Limit {
            return Limit::perMinute(120)->by('auth-api:user:' . ($request->user()?->getKey() ?? 'guest'));
        });

        RateLimiter::for('review', function (Request $request): Limit {
            return Limit::perMinutes(5, 3)->by('review:user:' . ($request->user()?->getKey() ?? 'guest'));
        });

        RateLimiter::for('chat', function (Request $request): Limit {
            if ($request->user()) {
                return Limit::perMinute(20)->by('chat:user:' . $request->user()->getKey());
            }

            return Limit::perMinute(6)->by('chat:ip:' . $request->ip());
        });
    }}
