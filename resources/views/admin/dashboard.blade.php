@extends('layouts.app')

@section('title', 'Admin Dashboard — EcoJourney')

@section('content')
<div class="container-eco py-16">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-forest-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-forest-700">
                <span class="h-1.5 w-1.5 rounded-full bg-forest-500"></span> Restricted Area
            </div>
            <h1 class="mt-3 font-display text-3xl font-extrabold tracking-tight text-gray-900">
                Admin Dashboard
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Welcome back, {{ auth()->user()->name }} ({{ auth()->user()->email }})
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="rounded-full bg-forest-100 px-4 py-1.5 text-xs font-bold text-forest-800 uppercase tracking-wider">
                Role: Admin
            </span>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-3 mb-10">
        <div class="glass rounded-3xl border border-gray-200 bg-white/80 p-6 shadow-lg">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Destinations</div>
            <div class="mt-2 font-display text-3xl font-extrabold text-forest-900">
                {{ \App\Models\Destination::count() }}
            </div>
        </div>
        <div class="glass rounded-3xl border border-gray-200 bg-white/80 p-6 shadow-lg">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Users</div>
            <div class="mt-2 font-display text-3xl font-extrabold text-forest-900">
                {{ \App\Models\User::count() }}
            </div>
        </div>
        <div class="glass rounded-3xl border border-gray-200 bg-white/80 p-6 shadow-lg">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Itineraries</div>
            <div class="mt-2 font-display text-3xl font-extrabold text-forest-900">
                {{ \App\Models\Itinerary::count() }}
            </div>
        </div>
    </div>

    <div class="glass rounded-3xl border border-gray-200 bg-white/80 p-8 shadow-xl">
        <h2 class="font-display text-xl font-bold text-gray-900 mb-4">Management Quick Actions</h2>
        <div class="grid gap-4 sm:grid-cols-3">
            <a href="{{ route('admin.destinations.index') }}" class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:border-forest-300 hover:shadow">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-forest-100 text-forest-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-gray-900">Manage Destinations</div>
                    <div class="text-xs text-gray-500">Create, edit, delete — admin only</div>
                </div>
            </a>
            <a href="{{ route('admin.hero.edit') }}" class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:border-forest-300 hover:shadow">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-forest-100 text-forest-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-gray-900">Hero Banner</div>
                    <div class="text-xs text-gray-500">Change the photo shown on the home page</div>
                </div>
            </a>
            <a href="{{ route('planner') }}" class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:border-forest-300 hover:shadow">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-forest-100 text-forest-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-gray-900">Journey Planner</div>
                    <div class="text-xs text-gray-500">Test the smart sustainable itinerary planner</div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
