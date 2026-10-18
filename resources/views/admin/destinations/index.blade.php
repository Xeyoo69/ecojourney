@extends('layouts.app')

@section('title', 'Manage Destinations — Admin')

@section('content')
<div class="container-eco py-10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-forest-600 hover:underline">← Back to Dashboard</a>
            <h1 class="mt-2 font-display text-3xl font-extrabold text-gray-900">Destinations</h1>
            <p class="mt-1 text-sm text-gray-500">Only admins can create, edit, or delete destinations.</p>
        </div>
        <a href="{{ route('admin.destinations.create') }}" class="eco-btn eco-btn-primary">+ New Destination</a>
    </div>

    @if(session('success'))
        <div class="mt-6 rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="mt-8 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Image</th>
                        <th class="px-5 py-3 text-left">Name</th>
                        <th class="px-5 py-3 text-left">Category</th>
                        <th class="px-5 py-3 text-left">Region</th>
                        <th class="px-5 py-3 text-left">Score</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($destinations as $d)
                        <tr class="hover:bg-gray-50/60">
                            <td class="px-5 py-4">
                                <img src="{{ $d->image_url }}" alt="{{ $d->name }}" class="h-14 w-20 rounded-xl object-cover border border-gray-200">
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-900">{{ $d->name }}</div>
                                <div class="text-xs text-gray-500">{{ $d->slug }} · {{ $d->location }}</div>
                            </td>
                            <td class="px-5 py-4 text-gray-600">{{ $d->category->name ?? '-' }}</td>
                            <td class="px-5 py-4 text-gray-600">{{ $d->region ?? '-' }}</td>
                            <td class="px-5 py-4"><span class="rounded-full bg-forest-50 px-3 py-1 text-xs font-bold text-forest-700">{{ $d->sustainability_score }}</span></td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.destinations.edit', $d) }}" class="rounded-full border border-gray-200 bg-white px-4 py-1.5 text-xs font-bold hover:bg-gray-50">Edit</a>
                                    <form action="{{ route('admin.destinations.destroy', $d) }}" method="POST" onsubmit="return confirm('Delete this destination?')">
                                        @csrf @method('DELETE')
                                        <button class="rounded-full bg-red-50 border border-red-200 px-4 py-1.5 text-xs font-bold text-red-700 hover:bg-red-100">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">No destinations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $destinations->links() }}</div>
</div>
@endsection
