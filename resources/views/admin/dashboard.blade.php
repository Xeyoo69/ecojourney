@extends('layouts.admin')

@section('title', 'Dashboard — EcoJourney Admin')

@section('content')
@php
    $stats = [
        ['label' => 'Destinations', 'value' => \App\Models\Destination::count(), 'note' => 'Published across the site', 'tone' => 'tone-forest', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>'],
        ['label' => 'Categories', 'value' => \App\Models\Category::count(), 'note' => 'Used for filtering', 'tone' => 'tone-amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>'],
        ['label' => 'Users', 'value' => \App\Models\User::count(), 'note' => 'Registered accounts', 'tone' => 'tone-blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>'],
        ['label' => 'Itineraries', 'value' => \App\Models\Itinerary::count(), 'note' => 'Trips planned by users', 'tone' => 'tone-violet', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>'],
    ];

    $heroCustom = (bool) \App\Models\Setting::get('hero_image');
    $recent = \App\Models\Destination::with('category')->latest()->take(5)->get();
@endphp

<div class="admin-page-head">
    <div>
        <span class="admin-eyebrow">Restricted area</span>
        <h1 class="admin-title">Dashboard</h1>
        <p class="admin-sub">
            Welcome back, <strong>{{ auth()->user()->name }}</strong> — here is what is happening across EcoJourney.
        </p>
    </div>

    <div class="admin-head-actions">
        <span class="badge badge-amber">Administrator</span>
        <a href="{{ route('admin.destinations.create') }}" class="admin-btn admin-btn-primary">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                <path d="M12 5v14M5 12h14" />
            </svg>
            New destination
        </a>
    </div>
</div>

{{-- STATS --}}
<div class="admin-stats">
    @foreach ($stats as $stat)
        <div class="admin-stat">
            <div class="admin-stat-top">
                <span class="admin-stat-icon {{ $stat['tone'] }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true">{!! $stat['icon'] !!}</svg>
                </span>
            </div>
            <div class="admin-stat-value">{{ number_format($stat['value']) }}</div>
            <div class="admin-stat-label">{{ $stat['label'] }}</div>
            <div class="admin-stat-note">{{ $stat['note'] }}</div>
        </div>
    @endforeach
</div>

<div class="grid gap-6 lg:grid-cols-3">

    {{-- QUICK ACTIONS --}}
    <section class="admin-card admin-card-pad lg:col-span-2">
        <div class="admin-section-head">
            <div>
                <h2 class="admin-section-title">Management</h2>
                <p class="admin-section-desc">Jump straight into the tools you use most.</p>
            </div>
        </div>

        <div class="admin-actions-grid">
            <a href="{{ route('admin.destinations.index') }}" class="admin-action">
                <span class="admin-action-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                    </svg>
                </span>
                <span>
                    <span class="admin-action-title">Destinations</span>
                    <span class="admin-action-desc">Create, edit and remove destinations shown on the site.</span>
                </span>
                <span class="admin-action-arrow">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M13.5 4.5L21 12l-7.5 7.5M21 12H3" />
                    </svg>
                </span>
            </a>

            <a href="{{ route('admin.hero.edit') }}" class="admin-action">
                <span class="admin-action-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 19.5h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
                    </svg>
                </span>
                <span>
                    <span class="admin-action-title">Hero banner</span>
                    <span class="admin-action-desc">
                        @if ($heroCustom)
                            A custom photo is live on the home page.
                        @else
                            Home page uses the top-rated destination photo.
                        @endif
                    </span>
                </span>
                <span class="admin-action-arrow">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M13.5 4.5L21 12l-7.5 7.5M21 12H3" />
                    </svg>
                </span>
            </a>

            <a href="{{ route('planner') }}" class="admin-action" target="_blank" rel="noopener">
                <span class="admin-action-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                    </svg>
                </span>
                <span>
                    <span class="admin-action-title">Journey planner</span>
                    <span class="admin-action-desc">Open the planner as a user and test the experience.</span>
                </span>
                <span class="admin-action-arrow">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M13.5 4.5L21 12l-7.5 7.5M21 12H3" />
                    </svg>
                </span>
            </a>
        </div>
    </section>

    {{-- RECENT --}}
    <section class="admin-card admin-card-pad">
        <div class="admin-section-head">
            <div>
                <h2 class="admin-section-title">Recently added</h2>
                <p class="admin-section-desc">Latest destinations by creation date.</p>
            </div>
        </div>

        @if ($recent->isEmpty())
            <p class="text-sm" style="color: var(--muted)">No destinations yet.</p>
        @else
            <div class="admin-list">
                @foreach ($recent as $d)
                    <div class="admin-row">
                        <img class="admin-thumb" src="{{ $d->image_url }}" alt="" loading="lazy">
                        <div class="min-w-0 flex-1">
                            <div class="admin-row-title truncate">{{ $d->name }}</div>
                            <div class="admin-row-sub truncate">{{ $d->location }}@if($d->region), {{ $d->region }}@endif</div>
                        </div>
                        <span class="badge badge-forest">{{ $d->sustainability_score }}</span>
                    </div>
                @endforeach
            </div>

            <a href="{{ route('admin.destinations.index') }}" class="admin-btn admin-btn-light admin-btn-sm mt-5 w-full">
                View all destinations
            </a>
        @endif
    </section>
</div>
@endsection
