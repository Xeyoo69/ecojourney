<?php

use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\HeroImageController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\DestinationController as ApiDestinationController;
use App\Http\Controllers\Api\ExploreController as ApiExploreController;
use App\Http\Controllers\Api\FavoriteController as ApiFavoriteController;
use App\Http\Controllers\Api\GoogleAuthController as ApiGoogleAuthController;
use App\Http\Controllers\Api\HomeController as ApiHomeController;
use App\Http\Controllers\Api\NationalController as ApiNationalController;
use App\Http\Controllers\Api\PlannerController as ApiPlannerController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SpaController;
use App\Http\Middleware\ApiNoStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->name('api.')->middleware(ApiNoStore::class)->group(function (): void {
    Route::middleware('throttle:public-api')->group(function (): void {
        Route::get('/home', ApiHomeController::class)->name('home');
        Route::get('/national', ApiNationalController::class)->name('national');
        Route::get('/categories', [ApiExploreController::class, 'categories'])->name('categories.index');
        Route::get('/destinations', [ApiExploreController::class, 'index'])->name('destinations.index');
        Route::get('/destinations/{slug}', [ApiDestinationController::class, 'show'])->name('destinations.show');
    });

    Route::middleware(['throttle:auth', 'guest'])->group(function (): void {
        Route::post('/auth/login', [ApiAuthController::class, 'login'])->name('auth.login');
        Route::post('/auth/register', [ApiAuthController::class, 'register'])->name('auth.register');
    });

    Route::post('/chat', [ChatController::class, 'send'])
        ->middleware('throttle:chat')
        ->name('chat.send');

    Route::middleware(['auth:sanctum', 'throttle:auth-api'])->group(function (): void {
        Route::get('/auth/user', [ApiAuthController::class, 'user'])->name('auth.user');
        Route::post('/auth/logout', [ApiAuthController::class, 'logout'])->name('auth.logout');
        Route::post('/planner/generate', [ApiPlannerController::class, 'generate'])->name('planner.generate');
        Route::get('/planner/result/{id}', [ApiPlannerController::class, 'result'])->whereNumber('id')->name('planner.result');
        Route::get('/favorites', [ApiFavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/favorites/{slug}', [ApiFavoriteController::class, 'store'])->name('favorites.store');
        Route::delete('/favorites/{slug}', [ApiFavoriteController::class, 'destroy'])->name('favorites.destroy');
        Route::delete('/favorites', [ApiFavoriteController::class, 'clear'])->name('favorites.clear');

        Route::post('/destinations/{slug}/reviews', [ReviewController::class, 'store'])
            ->middleware('throttle:review')
            ->name('reviews.store');
        Route::delete('/destinations/{slug}/reviews/{id}', [ReviewController::class, 'destroy'])
            ->name('reviews.destroy');
    });

    Route::get('/destinations/{slug}/reviews', [ReviewController::class, 'index'])
        ->middleware('throttle:public-api')
        ->name('reviews.index');
});

Route::get('/', SpaController::class)->name('home');

/*
|--------------------------------------------------------------------------
| SEO — sitemap & robots
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'id'], true)) {
        session(['locale' => $locale]);
    }

    return back();
})->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Explore
|--------------------------------------------------------------------------
*/

Route::get('/explore', SpaController::class)->name('explore');

Route::get('/national', SpaController::class)->name('national');

Route::get('/destinations/{slug}', SpaController::class)
    ->name('destinations.show');

/*
|--------------------------------------------------------------------------
| Planner — auth required
|--------------------------------------------------------------------------
*/

Route::get('/planner', SpaController::class)->name('planner');
Route::get('/planner/result/{id}', SpaController::class)->whereNumber('id')->name('planner.result');
Route::get('/favorites', SpaController::class)->name('favorites');

Route::get('/sustainability', SpaController::class)->name('sustainability');
Route::get('/about', SpaController::class)->name('about');
Route::get('/contact', SpaController::class)->name('contact');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', SpaController::class)->name('login');
Route::get('/register', SpaController::class)->name('register');

/*
|--------------------------------------------------------------------------
| Google OAuth — regular users only
|--------------------------------------------------------------------------
*/

Route::middleware(['guest', 'throttle:auth'])->group(function (): void {
    Route::get('/auth/google', [ApiGoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [ApiGoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::get('/hero', [HeroImageController::class, 'edit'])->name('hero.edit');
    Route::post('/hero', [HeroImageController::class, 'store'])->name('hero.store');
    Route::delete('/hero', [HeroImageController::class, 'destroy'])->name('hero.destroy');
    Route::resource('destinations', DestinationController::class)->except(['show']);
});

Route::fallback(SpaController::class);
