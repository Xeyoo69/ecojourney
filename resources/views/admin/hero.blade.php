@extends('layouts.admin')

@section('title', 'Hero banner — EcoJourney Admin')

@section('content')
@php
    $hasCustom = (bool) $heroImage;
    $src = $heroImage ?: ($fallback?->image_url ?? '');
@endphp

<div class="admin-page-head">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="admin-back">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M10.5 19.5L3 12l7.5-7.5M3 12h18" />
            </svg>
            Dashboard
        </a>
        <h1 class="admin-title">Hero banner</h1>
        <p class="admin-sub">The large photo at the very top of the home page.</p>
    </div>

    <div class="admin-head-actions">
        @if ($hasCustom)
            <span class="badge badge-amber">Custom photo active</span>
        @else
            <span class="badge badge-neutral">Using default</span>
        @endif
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-2">

    {{-- UPLOAD --}}
    <section class="admin-card admin-card-pad">
        <div class="admin-section-head">
            <div>
                <h2 class="admin-section-title">Upload a new photo</h2>
                <p class="admin-section-desc">Replaces the current hero immediately after saving.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="admin-alert admin-alert-error" role="alert" tabindex="-1" id="admin-validation-summary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p style="font-weight: 700">{{ $errors->first() }}</p>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.hero.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="admin-field @error('image') has-error @enderror">
                <label for="f-hero-image" class="admin-label">Photo file<span class="admin-required">*</span></label>
                <input id="f-hero-image" name="image" type="file" accept="image/jpeg,image/png" required
                       class="admin-file" aria-describedby="f-hero-hint" data-hero-input>

                <p class="admin-hint" id="f-hero-hint">
                    JPG or PNG up to 5 MB — a landscape crop around 1200 × 900 fills the banner without stretching.
                </p>

                @error('image')
                    <p class="admin-error">{{ $message }}</p>
                @enderror

                <div class="admin-preview" data-hero-preview hidden>
                    <span class="admin-preview-tag">New selection</span>
                    <img alt="Preview of the selected photo">
                </div>
            </div>

            <div class="admin-form-actions">
                <button type="submit" class="admin-btn admin-btn-primary">Save hero photo</button>
                <span style="font-size: 12.5px; color: var(--muted)">You can reset at any time.</span>
            </div>
        </form>

        @if ($hasCustom)
            <div style="margin-top: 22px; padding-top: 20px; border-top: 1px solid var(--border)">
                <h3 class="admin-section-title" style="font-size: 15px">Reset to default</h3>
                <p class="admin-section-desc" style="margin-bottom: 14px">
                    Removes your custom photo and falls back to the highest-scoring destination.
                </p>

                <form action="{{ route('admin.hero.destroy') }}" method="POST"
                      data-confirm="Remove the custom hero photo and fall back to the top-rated destination?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm">Reset to default</button>
                </form>
            </div>
        @endif
    </section>

    {{-- CURRENT --}}
    <section class="admin-card admin-card-pad">
        <div class="admin-section-head">
            <div>
                <h2 class="admin-section-title">Currently showing</h2>
                <p class="admin-section-desc">
                    @if ($hasCustom)
                        Your uploaded photo is live on the home page.
                    @elseif ($fallback)
                        No custom photo yet — using the top-rated destination.
                    @else
                        No destinations available to fall back to.
                    @endif
                </p>
            </div>
        </div>

        @if ($src)
            <div class="admin-preview" style="margin-top: 0">
                <span class="admin-preview-tag">{{ $hasCustom ? 'Custom' : 'Default' }}</span>
                <img src="{{ $src }}" alt="Current hero banner">
            </div>

            @if (! $hasCustom && $fallback)
                <div class="admin-row" style="padding-inline: 0; border-bottom: none">
                    <img class="admin-thumb" src="{{ $fallback->image_url }}" alt="">
                    <div class="min-w-0 flex-1">
                        <div class="admin-row-title truncate">{{ $fallback->name }}</div>
                        <div class="admin-row-sub truncate">{{ $fallback->location }}</div>
                    </div>
                    <span class="badge badge-forest">{{ $fallback->sustainability_score }}</span>
                </div>
            @endif

            <a href="{{ route('home') }}" class="admin-btn admin-btn-light admin-btn-sm" style="margin-top: 16px" target="_blank" rel="noopener">
                Preview on home page
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
            </a>
        @else
            <div class="admin-empty">
                <div class="admin-empty-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 19.5h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
                    </svg>
                </div>
                <p class="admin-empty-title">Nothing to show yet</p>
                <p class="admin-empty-text">Upload a photo, or add a destination so the home page has a fallback image.</p>
            </div>
        @endif
    </section>
</div>

<script>
    (function () {
        var input = document.querySelector('[data-hero-input]');
        var preview = document.querySelector('[data-hero-preview]');

        if (input && preview) {
            input.addEventListener('change', function () {
                var file = input.files && input.files[0];
                if (!file) { preview.hidden = true; return; }

                var img = preview.querySelector('img');
                if (img.src) { URL.revokeObjectURL(img.src); }
                img.src = URL.createObjectURL(file);
                preview.hidden = false;
            });
        }

        var summary = document.getElementById('admin-validation-summary');
        if (summary) { summary.focus(); }
    })();
</script>
@endsection
