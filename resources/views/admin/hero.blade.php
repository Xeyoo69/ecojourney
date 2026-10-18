@extends('layouts.app')

@section('title', 'Hero Banner — Admin')

@section('content')
<div class="container-eco py-10">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-forest-600 hover:underline">← Back to Dashboard</a>
        <h1 class="mt-2 font-display text-3xl font-extrabold text-gray-900">Hero Banner</h1>
        <p class="mt-1 text-sm text-gray-500">Choose the photo shown in the big banner at the top of the home page.</p>
    </div>

    @if(session('success'))
        <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="mt-8 grid gap-8 lg:grid-cols-2">
        <div class="glass rounded-3xl border border-gray-200 bg-white/80 p-8 shadow-xl">
            <h2 class="font-display text-xl font-bold text-gray-900">Upload new photo</h2>
            <p class="mt-1 text-sm text-gray-500">JPG or PNG, up to 5 MB. A landscape photo around 1200×900 works best.</p>
            <form action="{{ route('admin.hero.store') }}" method="POST" enctype="multipart/form-data" class="mt-6">
                @csrf
                <input type="file" name="image" accept="image/jpeg,image/png" required class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-forest-500 focus:ring-4 focus:ring-forest-500/10">
                @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                <button type="submit" class="eco-btn eco-btn-primary mt-4">Save hero photo</button>
            </form>

            @if ($heroImage)
                <form action="{{ route('admin.hero.destroy') }}" method="POST" class="mt-8 border-t border-gray-100 pt-6" onsubmit="return confirm('Reset the hero to the top-rated destination photo?')">
                    @csrf @method('DELETE')
                    <p class="text-sm text-gray-600">A custom hero photo is active. Reset to use the top-rated destination photo instead.</p>
                    <button type="submit" class="mt-3 cursor-pointer rounded-full border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-rose-300 hover:text-rose-600">Reset to default</button>
                </form>
            @endif
        </div>

        <div class="glass rounded-3xl border border-gray-200 bg-white/80 p-8 shadow-xl">
            <h2 class="font-display text-xl font-bold text-gray-900">Current hero</h2>
            <p class="mt-1 text-sm text-gray-500">
                @if ($heroImage)
                    Custom photo uploaded from the admin panel.
                @else
                    No custom photo yet — the home page uses the top-rated destination
                    <strong>{{ $fallback?->name ?? '—' }}</strong>.
                @endif
            </p>
            <div class="relative mt-6 h-72 overflow-hidden rounded-3xl border border-gray-100 bg-forest-50">
                <img src="{{ $heroImage ?: ($fallback?->image_url ?? '') }}" alt="Current hero" class="h-full w-full object-cover">
            </div>
        </div>
    </div>
</div>
@endsection
