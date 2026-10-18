@extends('layouts.admin')

@section('title', 'Edit ' . $destination->name . ' — EcoJourney Admin')

@section('content')
<div class="admin-page-head">
    <div>
        <a href="{{ route('admin.destinations.index') }}" class="admin-back">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M10.5 19.5L3 12l7.5-7.5M3 12h18" />
            </svg>
            Destinations
        </a>
        <h1 class="admin-title">{{ $destination->name }}</h1>
        <p class="admin-sub">
            Editing <code style="font-weight: 700">/destinations/{{ $destination->slug }}</code>
            @if ($destination->region) · {{ $destination->region }} @endif
        </p>
    </div>

    <div class="admin-head-actions">
        <a href="{{ url('/destinations/' . $destination->slug) }}" class="admin-btn admin-btn-light" target="_blank" rel="noopener">
            View on site
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
            </svg>
        </a>
    </div>
</div>

<form action="{{ route('admin.destinations.update', $destination) }}" method="POST" enctype="multipart/form-data"
      class="admin-card admin-card-pad" style="max-width: 900px">
    @csrf
    @method('PUT')
    @include('admin.destinations.form')
</form>
@endsection
