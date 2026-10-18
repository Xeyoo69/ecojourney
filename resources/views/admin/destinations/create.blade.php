@extends('layouts.admin')

@section('title', 'New destination — EcoJourney Admin')

@section('content')
<div class="admin-page-head">
    <div>
        <a href="{{ route('admin.destinations.index') }}" class="admin-back">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M10.5 19.5L3 12l7.5-7.5M3 12h18" />
            </svg>
            Destinations
        </a>
        <h1 class="admin-title">New destination</h1>
        <p class="admin-sub">Four short sections — the destination appears on the site as soon as you save it.</p>
    </div>
</div>

<form action="{{ route('admin.destinations.store') }}" method="POST" enctype="multipart/form-data"
      class="admin-card admin-card-pad" style="max-width: 900px">
    @csrf
    @include('admin.destinations.form')
</form>
@endsection
