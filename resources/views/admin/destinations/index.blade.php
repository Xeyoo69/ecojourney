@extends('layouts.admin')

@section('title', 'Destinations — EcoJourney Admin')

@section('content')
<div class="admin-page-head">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="admin-back">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M10.5 19.5L3 12l7.5-7.5M3 12h18" />
            </svg>
            Dashboard
        </a>
        <h1 class="admin-title">Destinations</h1>
        <p class="admin-sub">Create, edit and remove the destinations that power the public site.</p>
    </div>

    <div class="admin-head-actions">
        <a href="{{ route('admin.destinations.create') }}" class="admin-btn admin-btn-primary">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                <path d="M12 5v14M5 12h14" />
            </svg>
            New destination
        </a>
    </div>
</div>

<div class="admin-toolbar">
    <form action="{{ route('admin.destinations.index') }}" method="GET" role="search" class="admin-search">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
        </svg>
        <label for="destination-search" class="sr-only" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0)">Search destinations</label>
        <input
            id="destination-search"
            class="admin-input"
            type="search"
            name="q"
            value="{{ $q }}"
            placeholder="Search name, location or region…"
            autocomplete="off"
        >
        <button type="submit" class="sr-only" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0)">Search</button>
    </form>

    <div class="flex flex-wrap items-center gap-3">
        @if ($q !== '')
            <a href="{{ route('admin.destinations.index') }}" class="chip">
                Clear “{{ $q }}”
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18" />
                </svg>
            </a>
        @endif
        <span class="badge badge-neutral">{{ number_format($destinations->total()) }} total</span>
    </div>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th scope="col">Destination</th>
                <th scope="col">Category</th>
                <th scope="col">Region</th>
                <th scope="col">Score</th>
                <th scope="col" class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($destinations as $d)
                @php
                    $score = (int) $d->sustainability_score;
                    $tone = $score >= 70 ? 'score-hi' : ($score >= 40 ? 'score-mid' : 'score-low');
                @endphp
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <img class="admin-thumb" src="{{ $d->image_url }}" alt="" loading="lazy">
                            <div class="min-w-0">
                                <div class="admin-row-title">{{ $d->name }}</div>
                                <div class="admin-row-sub">{{ $d->location }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-forest">{{ $d->category->name ?? 'Uncategorised' }}</span>
                    </td>
                    <td style="color: var(--muted)">{{ $d->region ?: '—' }}</td>
                    <td>
                        <span class="admin-score">
                            <span class="admin-score-track"><span class="admin-score-fill {{ $tone }}" style="width: {{ $score }}%"></span></span>
                            <strong style="font-size: 13px">{{ $score }}</strong>
                        </span>
                    </td>
                    <td class="col-actions">
                        <div class="flex justify-end gap-2">
                            <a href="{{ url('/destinations/' . $d->slug) }}" class="chip" target="_blank" rel="noopener" title="Open on the public site">View</a>
                            <a href="{{ route('admin.destinations.edit', $d) }}" class="chip">Edit</a>
                            <form action="{{ route('admin.destinations.destroy', $d) }}" method="POST" data-confirm="Delete “{{ $d->name }}”? This cannot be undone.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="chip chip-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="admin-empty">
                            <div class="admin-empty-icon">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                                </svg>
                            </div>

                            @if ($q !== '')
                                <p class="admin-empty-title">No matches for “{{ $q }}”</p>
                                <p class="admin-empty-text">Try a different keyword, or clear the search to see every destination.</p>
                                <a href="{{ route('admin.destinations.index') }}" class="admin-btn admin-btn-light admin-btn-sm mt-5">Clear search</a>
                            @else
                                <p class="admin-empty-title">No destinations yet</p>
                                <p class="admin-empty-text">Add your first destination and it will appear across the site.</p>
                                <a href="{{ route('admin.destinations.create') }}" class="admin-btn admin-btn-primary admin-btn-sm mt-5">Create destination</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('admin.partials.pagination', ['paginator' => $destinations])
@endsection
