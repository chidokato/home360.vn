<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ApartmentImage;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ApartmentController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->filled('project_id') ? (int) $request->input('project_id') : null;

        $apartments = Apartment::query()
            ->with(['project', 'images'])
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('backend.apartments.index', [
            'apartments' => $apartments,
            'projects' => $this->projectOptions(),
            'projectId' => $projectId,
        ]);
    }

    public function create(Request $request)
    {
        return view('backend.apartments.create', [
            'projects' => $this->projectOptions(),
            'selectedProjectId' => $request->filled('project_id') ? (int) $request->input('project_id') : null,
            'galleryImages' => collect(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateApartment($request);

        $apartment = Apartment::create([
            'project_id' => $validated['project_id'],
            'name' => $validated['name'],
            'content' => $validated['content'] ?? null,
            'price' => $this->normalizePrice($validated),
            'area' => $validated['area'] ?? null,
            'bedroom_count' => $validated['bedroom_count'] ?? null,
            'bathroom_count' => $validated['bathroom_count'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->syncImages($request, $apartment);

        return redirect()
            ->route('backend.apartments.index', ['project_id' => $apartment->project_id])
            ->with('success', 'Them can ho thanh cong.');
    }

    public function edit(Apartment $apartment)
    {
        $apartment->load(['project', 'images']);

        return view('backend.apartments.edit', [
            'apartment' => $apartment,
            'projects' => $this->projectOptions(),
            'selectedProjectId' => $apartment->project_id,
            'galleryImages' => $apartment->images,
        ]);
    }

    public function update(Request $request, Apartment $apartment)
    {
        $validated = $this->validateApartment($request);

        $apartment->update([
            'project_id' => $validated['project_id'],
            'name' => $validated['name'],
            'content' => $validated['content'] ?? null,
            'price' => $this->normalizePrice($validated),
            'area' => $validated['area'] ?? null,
            'bedroom_count' => $validated['bedroom_count'] ?? null,
            'bathroom_count' => $validated['bathroom_count'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->syncImages($request, $apartment);

        if ($request->boolean('save_stay')) {
            return redirect()
                ->route('backend.apartments.edit', $apartment)
                ->with('success', 'Cap nhat can ho thanh cong.');
        }

        return redirect()
            ->route('backend.apartments.index', ['project_id' => $apartment->project_id])
            ->with('success', 'Cap nhat can ho thanh cong.');
    }

    public function destroy(Apartment $apartment)
    {
        $projectId = $apartment->project_id;

        foreach ($apartment->images as $image) {
            $this->deleteImageIfExists($image->image);
        }

        $apartment->delete();

        return redirect()
            ->route('backend.apartments.index', ['project_id' => $projectId])
            ->with('success', 'Xoa can ho thanh cong.');
    }

    protected function validateApartment(Request $request): array
    {
        return $request->validate([
            'project_id' => [
                'required',
                'integer',
                Rule::exists('posts', 'id')->where(fn ($query) => $query->where('type', Post::TYPE_PRODUCT)),
            ],
            'name' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'price_unit' => ['nullable', Rule::in(['ty', 'trieu'])],
            'area' => ['nullable', 'numeric', 'min:0'],
            'bedroom_count' => ['nullable', 'integer', 'min:0'],
            'bathroom_count' => ['nullable', 'integer', 'min:0'],
            'gallery_files.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'remove_gallery_images' => ['nullable', 'array'],
            'remove_gallery_images.*' => ['integer'],
            'is_active' => ['nullable'],
        ]);
    }

    protected function normalizePrice(array $validated): ?float
    {
        if (! array_key_exists('price', $validated) || $validated['price'] === null) {
            return null;
        }

        $multiplier = ($validated['price_unit'] ?? 'ty') === 'trieu' ? 1000000 : 1000000000;

        return (float) $validated['price'] * $multiplier;
    }

    protected function projectOptions()
    {
        return Post::query()
            ->where('type', Post::TYPE_PRODUCT)
            ->orderBy('title')
            ->pluck('title', 'id');
    }

    protected function syncImages(Request $request, Apartment $apartment): void
    {
        $removeIds = collect($request->input('remove_gallery_images', []))
            ->map(fn ($id) => (int) $id)
            ->filter();

        if ($removeIds->isNotEmpty()) {
            $imagesToDelete = $apartment->images()->whereIn('id', $removeIds)->get();

            foreach ($imagesToDelete as $image) {
                $this->deleteImageIfExists($image->image);
                $image->delete();
            }
        }

        if (! $request->hasFile('gallery_files')) {
            return;
        }

        $sortOrder = (int) $apartment->images()->max('sort_order');

        foreach ($request->file('gallery_files') as $file) {
            if (! $file) {
                continue;
            }

            $sortOrder++;

            ApartmentImage::create([
                'apartment_id' => $apartment->id,
                'image' => $this->storeImage($file),
                'sort_order' => $sortOrder,
            ]);
        }
    }

    protected function storeImage($file): string
    {
        $directory = public_path('uploads/apartments');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = now()->format('YmdHis') . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/apartments/' . $filename;
    }

    protected function deleteImageIfExists(?string $imagePath): void
    {
        if (! $imagePath) {
            return;
        }

        $fullPath = public_path($imagePath);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
