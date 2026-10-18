@extends('layouts.app')
@section('title', 'Edit Destination — Admin')
@section('content')
<div class="container-eco py-10 max-w-3xl">
    <a href="{{ route('admin.destinations.index') }}" class="text-xs font-bold text-forest-600 hover:underline">← Back</a>
    <h1 class="mt-2 font-display text-3xl font-extrabold text-gray-900">Edit: {{ $destination->name }}</h1>
    <form action="{{ route('admin.destinations.update', $destination) }}" method="POST" enctype="multipart/form-data" class="mt-6 rounded-3xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm">
        @csrf @method('PUT')
        @include('admin.destinations.form')
        <div class="mt-6 flex gap-3">
            <button class="eco-btn eco-btn-primary">Save Changes</button>
            <a href="{{ route('admin.destinations.index') }}" class="eco-btn eco-btn-light">Cancel</a>
        </div>
    </form>
</div>
@endsection
