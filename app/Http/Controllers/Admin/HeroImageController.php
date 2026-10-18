<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Setting;
use App\Support\CacheKeys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroImageController extends Controller
{
    private const SETTING_KEY = 'hero_image';

    private const DISK = 'public';

    private const DIR = 'hero';

    public function edit()
    {
        $heroImage = Setting::get(self::SETTING_KEY);
        $fallback = Destination::with('category')
            ->orderByDesc('sustainability_score')
            ->orderBy('name')
            ->first();

        return view('admin.hero', [
            'heroImage' => $heroImage !== null ? asset('storage/' . $heroImage) : null,
            'fallback' => $fallback,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $old = Setting::get(self::SETTING_KEY);
        $path = $validated['image']->store(self::DIR, self::DISK);

        Setting::set(self::SETTING_KEY, $path);
        $this->deleteStoredImage($old);
        CacheKeys::flushSummaries();

        return redirect()->route('admin.hero.edit')->with('success', 'Hero image updated.');
    }

    public function destroy()
    {
        $old = Setting::get(self::SETTING_KEY);

        Setting::forget(self::SETTING_KEY);
        $this->deleteStoredImage($old);
        CacheKeys::flushSummaries();

        return redirect()->route('admin.hero.edit')->with('success', 'Hero image reset to the top-rated destination.');
    }

    private function deleteStoredImage(?string $image): void
    {
        if (! $image || str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return;
        }

        if (Storage::disk(self::DISK)->exists($image)) {
            Storage::disk(self::DISK)->delete($image);
        }
    }
}
