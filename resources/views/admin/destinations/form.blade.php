@php
$isEdit = isset($destination) && $destination->exists;
$destination = $destination ?? null;
@endphp
<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Name</label>
        <input name="name" value="{{ old('name', $destination->name ?? '') }}" required class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-forest-500 focus:ring-4 focus:ring-forest-500/10 outline-none">
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Slug</label>
        <input name="slug" value="{{ old('slug', $destination->slug ?? '') }}" placeholder="auto from name if empty" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-forest-500 focus:ring-4 focus:ring-forest-500/10 outline-none">
        @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Category</label>
        <select name="category_id" required class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-forest-500 focus:ring-4 focus:ring-forest-500/10 outline-none">
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $destination->category_id ?? '') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        @error('category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Sustainability Score (0-100)</label>
        <input type="number" name="sustainability_score" min="0" max="100" value="{{ old('sustainability_score', $destination->sustainability_score ?? 80) }}" required class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-forest-500 focus:ring-4 focus:ring-forest-500/10 outline-none">
        @error('sustainability_score')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Location</label>
        <input name="location" value="{{ old('location', $destination->location ?? '') }}" required class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-forest-500 focus:ring-4 focus:ring-forest-500/10 outline-none">
        @error('location')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Region</label>
        <input name="region" value="{{ old('region', $destination->region ?? '') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-forest-500 focus:ring-4 focus:ring-forest-500/10 outline-none">
        @error('region')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Latitude</label>
        <input type="number" step="any" name="latitude" value="{{ old('latitude', $destination->latitude ?? '') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-forest-500 focus:ring-4 focus:ring-forest-500/10 outline-none">
        @error('latitude')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Longitude</label>
        <input type="number" step="any" name="longitude" value="{{ old('longitude', $destination->longitude ?? '') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-forest-500 focus:ring-4 focus:ring-forest-500/10 outline-none">
        @error('longitude')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Image</label>
            <input type="file" name="image" accept="image/jpeg,image/png" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-forest-500 focus:ring-4 focus:ring-forest-500/10 outline-none">
        @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        @if ($destination && $destination->image)
                <div class="mt-2 flex items-center gap-3">
                    <img src="{{ $destination->image_url }}" alt="Preview" class="max-w-xs rounded border border-gray-200">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-forest-600 focus:ring-forest-500">
                        Remove current image
                    </label>
                </div>
            @endif
        </div>
    <div class="sm:col-span-2">
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Description</label>
        <textarea name="description" rows="4" required class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-forest-500 focus:ring-4 focus:ring-forest-500/10 outline-none">{{ old('description', $destination->description ?? '') }}</textarea>
        @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
