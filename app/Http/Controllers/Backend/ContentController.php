<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostFloorPlan;
use App\Models\PostImage;
use App\Models\Province;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ContentController extends Controller
{
    public function productIndex()
    {
        return $this->indexByType(Post::TYPE_PRODUCT);
    }

    public function productCreate()
    {
        return $this->createByType(Post::TYPE_PRODUCT);
    }

    public function productStore(Request $request)
    {
        return $this->storeByType($request, Post::TYPE_PRODUCT);
    }

    public function productEdit(Post $post)
    {
        return $this->editByType($post, Post::TYPE_PRODUCT);
    }

    public function productUpdate(Request $request, Post $post)
    {
        return $this->updateByType($request, $post, Post::TYPE_PRODUCT);
    }

    public function productDestroy(Post $post)
    {
        return $this->destroyByType($post, Post::TYPE_PRODUCT);
    }

    public function newsIndex()
    {
        return $this->indexByType(Post::TYPE_NEWS);
    }

    public function newsCreate()
    {
        return $this->createByType(Post::TYPE_NEWS);
    }

    public function newsStore(Request $request)
    {
        return $this->storeByType($request, Post::TYPE_NEWS);
    }

    public function newsEdit(Post $post)
    {
        return $this->editByType($post, Post::TYPE_NEWS);
    }

    public function newsUpdate(Request $request, Post $post)
    {
        return $this->updateByType($request, $post, Post::TYPE_NEWS);
    }

    public function newsDestroy(Post $post)
    {
        return $this->destroyByType($post, Post::TYPE_NEWS);
    }

    public function productToggleStatus(Post $post)
    {
        return $this->toggleStatusByType($post, Post::TYPE_PRODUCT);
    }

    public function productToggleFeatured(Post $post)
    {
        abort_unless($post->type === Post::TYPE_PRODUCT, 404);

        $post->update([
            'is_featured' => ! $post->is_featured,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cap nhat trang thai du an noi bat thanh cong.',
            'is_active' => $post->is_featured,
            'label' => $post->is_featured ? 'Bat' : 'Tat',
        ]);
    }

    public function newsToggleStatus(Post $post)
    {
        return $this->toggleStatusByType($post, Post::TYPE_NEWS);
    }

    public function uploadEditorImage(Request $request)
    {
        $validated = $request->validate([
            'upload' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        $path = $this->storeImage($validated['upload']);

        return response()->json([
            'url' => asset($path),
        ]);
    }

    protected function indexByType(string $type)
    {
        $posts = Post::query()
            ->with('category')
            ->where('type', $type)
            ->latest()
            ->paginate(10);

        return view('backend.contents.index', [
            'posts' => $posts,
            'type' => $type,
            'typeLabel' => Post::types()[$type],
        ]);
    }

    protected function createByType(string $type)
    {
        $selectedProvinceId = (int) old('province_id', 0) ?: null;

        return view('backend.contents.create', [
            'type' => $type,
            'typeLabel' => Post::types()[$type],
            'categories' => $this->categoryOptions($type),
            'sellerOptions' => $this->sellerOptions(),
            'provinceOptions' => $this->provinceOptions(),
            'wardOptions' => $this->wardOptionsForProvince($selectedProvinceId),
            'wardMap' => $this->wardMap(),
            'galleryImages' => collect(),
            'floorPlans' => $this->supportsFloorPlans() ? collect() : collect(),
        ]);
    }

    protected function storeByType(Request $request, string $type)
    {
        $validated = $this->validatePost($request, $type);

        $post = Post::create($this->payload($request, $validated, $type));
        $this->syncGalleryImages($request, $post, $type);
        $this->syncFloorPlans($request, $post, $type);

        return redirect()
            ->route($this->routePrefix($type) . '.index')
            ->with('success', 'Them ' . strtolower(Post::types()[$type]) . ' thanh cong.');
    }

    protected function editByType(Post $post, string $type)
    {
        abort_unless($post->type === $type, 404);

        $selectedProvinceId = (int) old('province_id', $post->province_id) ?: null;

        return view('backend.contents.edit', [
            'post' => $post,
            'type' => $type,
            'typeLabel' => Post::types()[$type],
            'categories' => $this->categoryOptions($type),
            'sellerOptions' => $this->sellerOptions(),
            'provinceOptions' => $this->provinceOptions(),
            'wardOptions' => $this->wardOptionsForProvince($selectedProvinceId),
            'wardMap' => $this->wardMap(),
            'galleryImages' => $post->galleryImages,
            'floorPlans' => $this->supportsFloorPlans() ? $post->floorPlans : collect(),
        ]);
    }

    protected function updateByType(Request $request, Post $post, string $type)
    {
        abort_unless($post->type === $type, 404);

        $validated = $this->validatePost($request, $type, $post->id);

        $post->update($this->payload($request, $validated, $type, $post));
        $this->syncGalleryImages($request, $post, $type);
        $this->syncFloorPlans($request, $post, $type);

        if ($request->boolean('save_stay')) {
            return redirect()
                ->route($this->routePrefix($type) . '.edit', $post)
                ->with('success', 'Cap nhat ' . strtolower(Post::types()[$type]) . ' thanh cong.');
        }

        return redirect()
            ->route($this->routePrefix($type) . '.index')
            ->with('success', 'Cap nhat ' . strtolower(Post::types()[$type]) . ' thanh cong.');
    }

    protected function destroyByType(Post $post, string $type)
    {
        abort_unless($post->type === $type, 404);

        $this->deleteImageIfExists($post->image);
        $this->deleteImageIfExists($post->location_image);
        foreach ($post->galleryImages as $image) {
            $this->deleteImageIfExists($image->image);
        }
        if ($this->supportsFloorPlans()) {
            foreach ($post->floorPlans as $floorPlan) {
                $this->deleteImageIfExists($floorPlan->image);
            }
        }
        $post->delete();

        return redirect()
            ->route($this->routePrefix($type) . '.index')
            ->with('success', 'Xoa ' . strtolower(Post::types()[$type]) . ' thanh cong.');
    }

    protected function toggleStatusByType(Post $post, string $type)
    {
        abort_unless($post->type === $type, 404);

        $post->update([
            'is_active' => ! $post->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cap nhat trang thai ' . strtolower(Post::types()[$type]) . ' thanh cong.',
            'is_active' => $post->is_active,
            'label' => $post->is_active ? 'Hien thi' : 'An',
        ]);
    }

    protected function validatePost(Request $request, string $type, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('type', $type)),
            ],
            'seller_id' => $type === Post::TYPE_PRODUCT
                ? ['nullable', 'integer', Rule::exists('users', 'id')]
                : ['nullable'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($ignoreId),
            ],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'summary' => ['nullable', 'string'],
            'sales_policy' => $type === Post::TYPE_PRODUCT ? ['nullable', 'string'] : ['nullable'],
            'content' => ['nullable', 'string'],
            'address' => $type === Post::TYPE_PRODUCT ? ['nullable', 'string', 'max:255'] : ['nullable'],
            'province_id' => $type === Post::TYPE_PRODUCT
                ? ['nullable', 'integer', Rule::exists('provinces', 'id')]
                : ['nullable'],
            'ward_id' => $type === Post::TYPE_PRODUCT
                ? ['nullable', 'integer', Rule::exists('wards', 'id')]
                : ['nullable'],
            'map_embed' => $type === Post::TYPE_PRODUCT ? ['nullable', 'string'] : ['nullable'],
            'location_image_file' => $type === Post::TYPE_PRODUCT
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048']
                : ['nullable'],
            'remove_location_image' => ['nullable'],
            'area_from' => $type === Post::TYPE_PRODUCT ? ['nullable', 'numeric', 'min:0'] : ['nullable'],
            'area_to' => $type === Post::TYPE_PRODUCT ? ['nullable', 'numeric', 'min:0'] : ['nullable'],
            'floor_count_from' => $type === Post::TYPE_PRODUCT ? ['nullable', 'integer', 'min:0'] : ['nullable'],
            'floor_count_to' => $type === Post::TYPE_PRODUCT ? ['nullable', 'integer', 'min:0'] : ['nullable'],
            'unit_count_from' => $type === Post::TYPE_PRODUCT ? ['nullable', 'integer', 'min:0'] : ['nullable'],
            'unit_count_to' => $type === Post::TYPE_PRODUCT ? ['nullable', 'integer', 'min:0'] : ['nullable'],
            'bedroom_count_from' => $type === Post::TYPE_PRODUCT ? ['nullable', 'integer', 'min:0'] : ['nullable'],
            'bedroom_count_to' => $type === Post::TYPE_PRODUCT ? ['nullable', 'integer', 'min:0'] : ['nullable'],
            'bathroom_count_from' => $type === Post::TYPE_PRODUCT ? ['nullable', 'integer', 'min:0'] : ['nullable'],
            'bathroom_count_to' => $type === Post::TYPE_PRODUCT ? ['nullable', 'integer', 'min:0'] : ['nullable'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'gallery_files.*' => $type === Post::TYPE_PRODUCT
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048']
                : ['nullable'],
            'gallery_files_interior.*' => $type === Post::TYPE_PRODUCT
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048']
                : ['nullable'],
            'gallery_files_perspective.*' => $type === Post::TYPE_PRODUCT
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048']
                : ['nullable'],
            'gallery_files_amenity.*' => $type === Post::TYPE_PRODUCT
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048']
                : ['nullable'],
            'remove_gallery_images' => ['nullable', 'array'],
            'remove_gallery_images.*' => ['integer'],
            'floor_plans' => $type === Post::TYPE_PRODUCT ? ['nullable', 'array'] : ['nullable'],
            'floor_plans.*.id' => $type === Post::TYPE_PRODUCT ? ['nullable', 'integer'] : ['nullable'],
            'floor_plans.*.name' => $type === Post::TYPE_PRODUCT ? ['nullable', 'string', 'max:255'] : ['nullable'],
            'floor_plans.*.existing_image' => $type === Post::TYPE_PRODUCT ? ['nullable', 'string', 'max:255'] : ['nullable'],
            'floor_plans.*.image_file' => $type === Post::TYPE_PRODUCT
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048']
                : ['nullable'],
            'remove_floor_plans' => ['nullable', 'array'],
            'remove_floor_plans.*' => ['integer'],
            'remove_image' => ['nullable'],
            'price' => $type === Post::TYPE_PRODUCT
                ? ['nullable', 'numeric', 'min:0']
                : ['nullable'],
            'price_unit' => $type === Post::TYPE_PRODUCT
                ? ['nullable', Rule::in(['ty', 'trieu'])]
                : ['nullable'],
            'published_at' => ['nullable', 'date'],
            'is_active' => ['nullable'],
            'is_featured' => $type === Post::TYPE_PRODUCT ? ['nullable'] : ['nullable'],
        ]);

        if ($this->containsInlineBase64Image($validated['content'] ?? null)) {
            throw ValidationException::withMessages([
                'content' => 'Noi dung dang chua anh base64. Vui long keo tha hoac dan anh de he thong upload thanh file truoc khi luu.',
            ]);
        }

        if ($this->containsInlineBase64Image($validated['sales_policy'] ?? null)) {
            throw ValidationException::withMessages([
                'sales_policy' => 'Chinh sach ban hang dang chua anh base64. Vui long upload anh thanh file truoc khi luu.',
            ]);
        }

        if (
            $type === Post::TYPE_PRODUCT
            && ! empty($validated['province_id'])
            && ! empty($validated['ward_id'])
            && ! Ward::query()
                ->whereKey($validated['ward_id'])
                ->where('province_id', $validated['province_id'])
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'ward_id' => 'Phuong xa khong thuoc tinh thanh da chon.',
            ]);
        }

        $this->validateRangeFields($validated, $type);
        $this->validateFloorPlans($request, $validated, $type);

        return $validated;
    }

    protected function payload(Request $request, array $validated, string $type, ?Post $post = null): array
    {
        $imagePath = $post->image ?? null;
        $locationImagePath = $post->location_image ?? null;

        if ($request->boolean('remove_image') && $imagePath) {
            $this->deleteImageIfExists($imagePath);
            $imagePath = null;
        }

        if ($request->hasFile('image_file')) {
            if ($imagePath) {
                $this->deleteImageIfExists($imagePath);
            }

            $imagePath = $this->storeImage($request->file('image_file'));
        }

        if ($type === Post::TYPE_PRODUCT && $request->boolean('remove_location_image') && $locationImagePath) {
            $this->deleteImageIfExists($locationImagePath);
            $locationImagePath = null;
        }

        if ($type === Post::TYPE_PRODUCT && $request->hasFile('location_image_file')) {
            if ($locationImagePath) {
                $this->deleteImageIfExists($locationImagePath);
            }

            $locationImagePath = $this->storeImage($request->file('location_image_file'));
        }

        $price = null;

        if ($type === Post::TYPE_PRODUCT && array_key_exists('price', $validated) && $validated['price'] !== null) {
            $multiplier = ($validated['price_unit'] ?? 'ty') === 'trieu' ? 1000000 : 1000000000;
            $price = (float) $validated['price'] * $multiplier;
        }

        return [
            'type' => $type,
            'category_id' => $validated['category_id'] ?? null,
            'seller_id' => $type === Post::TYPE_PRODUCT ? ($validated['seller_id'] ?? null) : null,
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?: Str::slug($validated['title']),
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'sales_policy' => $type === Post::TYPE_PRODUCT ? ($validated['sales_policy'] ?? null) : null,
            'content' => $validated['content'] ?? null,
            'address' => $type === Post::TYPE_PRODUCT ? ($validated['address'] ?? null) : null,
            'province_id' => $type === Post::TYPE_PRODUCT ? ($validated['province_id'] ?? null) : null,
            'ward_id' => $type === Post::TYPE_PRODUCT ? ($validated['ward_id'] ?? null) : null,
            'map_embed' => $type === Post::TYPE_PRODUCT ? ($validated['map_embed'] ?? null) : null,
            'location_image' => $type === Post::TYPE_PRODUCT ? $locationImagePath : null,
            'area' => null,
            'area_from' => $type === Post::TYPE_PRODUCT ? ($validated['area_from'] ?? null) : null,
            'area_to' => $type === Post::TYPE_PRODUCT ? ($validated['area_to'] ?? null) : null,
            'floor_count' => null,
            'floor_count_from' => $type === Post::TYPE_PRODUCT ? ($validated['floor_count_from'] ?? null) : null,
            'floor_count_to' => $type === Post::TYPE_PRODUCT ? ($validated['floor_count_to'] ?? null) : null,
            'unit_count' => null,
            'unit_count_from' => $type === Post::TYPE_PRODUCT ? ($validated['unit_count_from'] ?? null) : null,
            'unit_count_to' => $type === Post::TYPE_PRODUCT ? ($validated['unit_count_to'] ?? null) : null,
            'bedroom_count' => null,
            'bedroom_count_from' => $type === Post::TYPE_PRODUCT ? ($validated['bedroom_count_from'] ?? null) : null,
            'bedroom_count_to' => $type === Post::TYPE_PRODUCT ? ($validated['bedroom_count_to'] ?? null) : null,
            'bathroom_count' => null,
            'bathroom_count_from' => $type === Post::TYPE_PRODUCT ? ($validated['bathroom_count_from'] ?? null) : null,
            'bathroom_count_to' => $type === Post::TYPE_PRODUCT ? ($validated['bathroom_count_to'] ?? null) : null,
            'image' => $imagePath,
            'price' => $type === Post::TYPE_PRODUCT ? $price : null,
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $type === Post::TYPE_PRODUCT ? $request->boolean('is_featured') : false,
            'published_at' => $validated['published_at'] ?? null,
        ];
    }

    protected function categoryOptions(string $type)
    {
        return Category::query()
            ->where('type', $type)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    protected function provinceOptions()
    {
        return Province::query()
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    protected function wardOptionsForProvince(?int $provinceId)
    {
        if (! $provinceId) {
            return collect();
        }

        return Ward::query()
            ->where('province_id', $provinceId)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    protected function wardMap(): array
    {
        return Ward::query()
            ->orderBy('province_id')
            ->orderBy('name')
            ->get(['id', 'province_id', 'name'])
            ->groupBy('province_id')
            ->map(function ($wards) {
                return $wards->map(function (Ward $ward) {
                    return [
                        'id' => $ward->id,
                        'name' => $ward->name,
                    ];
                })->values()->all();
            })
            ->toArray();
    }

    protected function sellerOptions()
    {
        return User::query()
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    protected function routePrefix(string $type): string
    {
        return $type === Post::TYPE_PRODUCT ? 'backend.products' : 'backend.news';
    }

    protected function syncGalleryImages(Request $request, Post $post, string $type): void
    {
        if ($type !== Post::TYPE_PRODUCT) {
            return;
        }

        if (! $this->supportsFloorPlans()) {
            return;
        }

        $removeIds = collect($request->input('remove_gallery_images', []))
            ->map(fn ($id) => (int) $id)
            ->filter();

        if ($removeIds->isNotEmpty()) {
            $imagesToDelete = $post->galleryImages()->whereIn('id', $removeIds)->get();

            foreach ($imagesToDelete as $image) {
                $this->deleteImageIfExists($image->image);
                $image->delete();
            }
        }

        $sortOrder = (int) $post->galleryImages()->max('sort_order');

        $galleryInputs = [
            'gallery_files' => PostImage::TYPE_PERSPECTIVE,
            'gallery_files_interior' => PostImage::TYPE_INTERIOR,
            'gallery_files_perspective' => PostImage::TYPE_PERSPECTIVE,
            'gallery_files_amenity' => PostImage::TYPE_AMENITY,
        ];

        foreach ($galleryInputs as $inputName => $imageType) {
            if (! $request->hasFile($inputName)) {
                continue;
            }

            foreach ($request->file($inputName) as $file) {
                if (! $file) {
                    continue;
                }

                $sortOrder++;

                PostImage::create([
                    'post_id' => $post->id,
                    'image' => $this->storeImage($file),
                    'image_type' => $imageType,
                    'sort_order' => $sortOrder,
                ]);
            }
        }
    }

    protected function syncFloorPlans(Request $request, Post $post, string $type): void
    {
        if ($type !== Post::TYPE_PRODUCT) {
            return;
        }

        try {
            $removeIds = collect($request->input('remove_floor_plans', []))
                ->map(fn ($id) => (int) $id)
                ->filter();

            if ($removeIds->isNotEmpty()) {
                $floorPlansToDelete = $post->floorPlans()->whereIn('id', $removeIds)->get();

                foreach ($floorPlansToDelete as $floorPlan) {
                    $this->deleteImageIfExists($floorPlan->image);
                    $floorPlan->delete();
                }
            }

            $items = collect($request->input('floor_plans', []))->values();
            $uploadedFiles = $request->file('floor_plans', []);
            $existingFloorPlans = $post->floorPlans()->get()->keyBy('id');
            $sortOrder = 0;

            foreach ($items as $index => $item) {
                $floorPlanId = isset($item['id']) ? (int) $item['id'] : null;

                if ($floorPlanId && $removeIds->contains($floorPlanId)) {
                    continue;
                }

                $name = trim((string) ($item['name'] ?? ''));
                $existingFloorPlan = $floorPlanId ? $existingFloorPlans->get($floorPlanId) : null;
                $imageFile = $uploadedFiles[$index]['image_file'] ?? null;

                if (! $existingFloorPlan && $name === '' && ! $imageFile) {
                    continue;
                }

                $sortOrder++;

                if ($existingFloorPlan) {
                    $imagePath = $existingFloorPlan->image;

                    if ($imageFile) {
                        $this->deleteImageIfExists($imagePath);
                        $imagePath = $this->storeImage($imageFile);
                    }

                    $existingFloorPlan->update([
                        'name' => $name,
                        'image' => $imagePath,
                        'sort_order' => $sortOrder,
                    ]);

                    continue;
                }

                if (! $imageFile) {
                    continue;
                }

                PostFloorPlan::create([
                    'post_id' => $post->id,
                    'name' => $name,
                    'image' => $this->storeImage($imageFile),
                    'sort_order' => $sortOrder,
                ]);
            }
        } catch (QueryException $exception) {
            if (($exception->errorInfo[1] ?? null) !== 1146) {
                throw $exception;
            }
        }
    }

    protected function storeImage($file): string
    {
        $directory = public_path('uploads/posts');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = now()->format('YmdHis') . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/posts/' . $filename;
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

    protected function containsInlineBase64Image(?string $content): bool
    {
        if (! $content) {
            return false;
        }

        return Str::contains($content, 'src="data:image/')
            || Str::contains($content, "src='data:image/")
            || Str::contains($content, 'src=data:image/');
    }

    protected function validateRangeFields(array $validated, string $type): void
    {
        if ($type !== Post::TYPE_PRODUCT) {
            return;
        }

        $ranges = [
            'area' => ['from' => 'area_from', 'to' => 'area_to', 'label' => 'Dien tich'],
            'floor_count' => ['from' => 'floor_count_from', 'to' => 'floor_count_to', 'label' => 'So tang'],
            'unit_count' => ['from' => 'unit_count_from', 'to' => 'unit_count_to', 'label' => 'So can'],
            'bedroom_count' => ['from' => 'bedroom_count_from', 'to' => 'bedroom_count_to', 'label' => 'So phong ngu'],
            'bathroom_count' => ['from' => 'bathroom_count_from', 'to' => 'bathroom_count_to', 'label' => 'So WC'],
        ];

        $messages = [];

        foreach ($ranges as $range) {
            $from = $validated[$range['from']] ?? null;
            $to = $validated[$range['to']] ?? null;

            if ($from !== null && $to !== null && (float) $to < (float) $from) {
                $messages[$range['to']] = $range['label'] . ' den phai lon hon hoac bang gia tri tu.';
            }
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }
    }

    protected function validateFloorPlans(Request $request, array $validated, string $type): void
    {
        if ($type !== Post::TYPE_PRODUCT) {
            return;
        }

        if (! $this->supportsFloorPlans()) {
            return;
        }

        $floorPlans = collect($validated['floor_plans'] ?? [])->values();
        $uploadedFiles = $request->file('floor_plans', []);
        $messages = [];

        foreach ($floorPlans as $index => $item) {
            $name = trim((string) ($item['name'] ?? ''));
            $hasExistingId = ! empty($item['id']);
            $hasExistingImage = filled($item['existing_image'] ?? null);
            $hasNewImage = isset($uploadedFiles[$index]['image_file']) && $uploadedFiles[$index]['image_file'] !== null;

            if ($name === '' && ! $hasExistingId && ! $hasExistingImage && ! $hasNewImage) {
                continue;
            }

            if ($name === '') {
                $messages["floor_plans.$index.name"] = 'Ten mat bang khong duoc de trong.';
            }

            if (! $hasExistingImage && ! $hasNewImage) {
                $messages["floor_plans.$index.image_file"] = 'Anh mat bang khong duoc de trong.';
            }
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }
    }

    protected function supportsFloorPlans(): bool
    {
        return Schema::hasTable('post_floor_plans');
    }
}
