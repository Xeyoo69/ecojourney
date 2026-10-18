<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DestinationController extends Controller
{
    private const IMAGE_RULES = ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'];

    public function index()
    {
        $destinations = Destination::with('category')->latest()->paginate(12);

        return view('admin.destinations.index', compact('destinations'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.destinations.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->normalizeSlug($request);

        $validated = $request->validate($this->rules());
        $validated['image'] = $this->storeUploadedImage($request);

        Destination::create($validated);

        return redirect()->route('admin.destinations.index')->with('success', 'Destination created.');
    }

    public function edit(Destination $destination)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.destinations.edit', compact('destination', 'categories'));
    }

    public function update(Request $request, Destination $destination)
    {
        $this->normalizeSlug($request);

        $validated = $request->validate($this->rules($destination));
        $uploaded = $this->storeUploadedImage($request);

        if ($uploaded !== null) {
            $this->deleteStoredImage($destination->image);
            $validated['image'] = $uploaded;
        } elseif ($request->boolean('remove_image')) {
            $this->deleteStoredImage($destination->image);
            $validated['image'] = null;
        }

        $destination->update($validated);

        return redirect()->route('admin.destinations.index')->with('success', 'Destination updated.');
    }

    public function destroy(Destination $destination)
    {
        $this->deleteStoredImage($destination->image);
        $destination->delete();

        return redirect()->route('admin.destinations.index')->with('success', 'Destination deleted.');
    }

    private function normalizeSlug(Request $request): void
    {
        $request->merge([
            'slug' => Str::slug((string) ($request->input('slug') ?: $request->input('name'))),
        ]);
    }

    private function rules(?Destination $destination = null): array
    {
        $slugRule = 'unique:destinations,slug'.($destination ? ','.$destination->id : '');

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', $slugRule],
            'location' => ['required', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => self::IMAGE_RULES,
            'remove_image' => ['nullable', 'boolean'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'sustainability_score' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }

    private function storeUploadedImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('destinations', 'public');
    }

    private function deleteStoredImage(?string $image): void
    {
        if (! $image || str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return;
        }

        if (Storage::disk('public')->exists($image)) {
            Storage::disk('public')->delete($image);
        }
    }
}
