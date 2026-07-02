<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $groupedTrees = collect(Category::types())->mapWithKeys(function ($label, $type) use ($categories) {
            return [$type => $this->buildTree($categories->where('type', $type))];
        });

        $types = Category::types();

        return view('backend.categories.index', compact('groupedTrees', 'types'));
    }

    public function create()
    {
        $parentOptions = $this->flattenCategoriesByType();
        $types = Category::types();

        return view('backend.categories.create', compact('parentOptions', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);

        if ($validated['parent_id']) {
            $parentCategory = Category::find($validated['parent_id']);

            if ($parentCategory && $parentCategory->type !== $validated['type']) {
                return back()
                    ->withErrors(['parent_id' => 'Danh muc cha phai cung loai voi category hien tai.'])
                    ->withInput();
            }
        }

        Category::create([
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: Str::slug($validated['name']),
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('backend.categories.index')
            ->with('success', 'Them category thanh cong.');
    }

    public function edit(Category $category)
    {
        $parentOptions = $this->flattenCategoriesByType($category->id);
        $types = Category::types();

        return view('backend.categories.edit', compact('category', 'parentOptions', 'types'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $this->validateCategory($request, $category->id);

        if ($validated['parent_id'] && $this->isDescendant($category, (int) $validated['parent_id'])) {
            return back()
                ->withErrors(['parent_id' => 'Khong the chon danh muc con lam danh muc cha.'])
                ->withInput();
        }

        if ($validated['parent_id']) {
            $parentCategory = Category::find($validated['parent_id']);

            if ($parentCategory && $parentCategory->type !== $validated['type']) {
                return back()
                    ->withErrors(['parent_id' => 'Danh muc cha phai cung loai voi category hien tai.'])
                    ->withInput();
            }
        }

        $category->update([
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: Str::slug($validated['name']),
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('backend.categories.index')
            ->with('success', 'Cap nhat category thanh cong.');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return redirect()
                ->route('backend.categories.index')
                ->with('error', 'Khong the xoa category dang co danh muc con.');
        }

        $category->delete();

        return redirect()
            ->route('backend.categories.index')
            ->with('success', 'Xoa category thanh cong.');
    }

    public function toggleStatus(Category $category)
    {
        $category->update([
            'is_active' => ! $category->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cap nhat trang thai category thanh cong.',
            'is_active' => $category->is_active,
            'label' => $category->is_active ? 'Hien thi' : 'An',
        ]);
    }

    protected function validateCategory(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(array_keys(Category::types()))],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($ignoreId),
            ],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable'],
        ]);
    }

    protected function buildTree(Collection $categories, ?int $parentId = null): Collection
    {
        return $categories
            ->where('parent_id', $parentId)
            ->values()
            ->map(function (Category $category) use ($categories) {
                $category->children_tree = $this->buildTree($categories, $category->id);

                return $category;
            });
    }

    protected function flattenCategoriesByType(?int $excludeId = null): array
    {
        $categories = Category::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return collect(Category::types())->mapWithKeys(function ($label, $type) use ($categories, $excludeId) {
            $tree = $this->buildTree($categories->where('type', $type));
            $options = [];

            $walk = function (Collection $nodes, string $prefix = '') use (&$walk, &$options, $excludeId): void {
                foreach ($nodes as $node) {
                    if ($excludeId !== null && $node->id === $excludeId) {
                        continue;
                    }

                    $options[$node->id] = $prefix . $node->name;
                    $walk($node->children_tree, $prefix . '-- ');
                }
            };

            $walk($tree);

            return [$type => $options];
        })->toArray();
    }

    protected function isDescendant(Category $category, int $parentId): bool
    {
        $currentParent = Category::find($parentId);

        while ($currentParent) {
            if ($currentParent->id === $category->id) {
                return true;
            }

            $currentParent = $currentParent->parent;
        }

        return false;
    }
}
