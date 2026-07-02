<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $menuTree = $this->buildTree($menus);

        return view('backend.menus.index', compact('menuTree'));
    }

    public function create()
    {
        $parentOptions = $this->flattenMenus();

        return view('backend.menus.create', compact('parentOptions'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateMenu($request);

        Menu::create([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['name']),
            'target' => $validated['target'] ?? '_self',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('backend.menus.index')
            ->with('success', 'Them menu thanh cong.');
    }

    public function edit(Menu $menu)
    {
        $parentOptions = $this->flattenMenus($menu->id);

        return view('backend.menus.edit', compact('menu', 'parentOptions'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $this->validateMenu($request, $menu->id);

        if ($validated['parent_id'] && $this->isDescendant($menu, (int) $validated['parent_id'])) {
            return back()
                ->withErrors(['parent_id' => 'Khong the chon menu con lam menu cha.'])
                ->withInput();
        }

        $menu->update([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['name'], $menu->id),
            'target' => $validated['target'] ?? '_self',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('backend.menus.index')
            ->with('success', 'Cap nhat menu thanh cong.');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->children()->exists()) {
            return redirect()
                ->route('backend.menus.index')
                ->with('error', 'Khong the xoa menu dang co menu con.');
        }

        $menu->delete();

        return redirect()
            ->route('backend.menus.index')
            ->with('success', 'Xoa menu thanh cong.');
    }

    public function toggleStatus(Menu $menu)
    {
        $menu->update([
            'is_active' => ! $menu->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cap nhat trang thai menu thanh cong.',
            'is_active' => $menu->is_active,
            'label' => $menu->is_active ? 'Hien thi' : 'An',
        ]);
    }

    public function updateSortOrder(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $menu->update([
            'sort_order' => $validated['sort_order'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cap nhat thu tu menu thanh cong.',
            'sort_order' => $menu->sort_order,
        ]);
    }

    protected function validateMenu(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:menus,id'],
            'name' => ['required', 'string', 'max:255'],
            'target' => ['nullable', Rule::in(['_self', '_blank'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable'],
        ]);
    }

    protected function buildTree(Collection $menus, ?int $parentId = null): Collection
    {
        return $menus
            ->where('parent_id', $parentId)
            ->values()
            ->map(function (Menu $menu) use ($menus) {
                $menu->children_tree = $this->buildTree($menus, $menu->id);

                return $menu;
            });
    }

    protected function flattenMenus(?int $excludeId = null): array
    {
        $menus = Menu::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $tree = $this->buildTree($menus);
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

        return $options;
    }

    protected function isDescendant(Menu $menu, int $parentId): bool
    {
        $currentParent = Menu::find($parentId);

        while ($currentParent) {
            if ($currentParent->id === $menu->id) {
                return true;
            }

            $currentParent = $currentParent->parent;
        }

        return false;
    }

    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug !== '' ? $baseSlug : 'menu';
        $counter = 1;

        while (
            Menu::query()
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
