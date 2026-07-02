<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\SeoConfig;
use App\Models\Setting;
use Illuminate\Support\Collection;

abstract class BaseFrontendController extends Controller
{
    protected function sharedViewData(array $data = []): array
    {
        $settings = Setting::query()->first();
        $menus = Menu::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $productCategorySlugs = Category::query()
            ->where('type', Category::TYPE_PRODUCT)
            ->where('is_active', true)
            ->pluck('slug')
            ->flip();

        $newsCategorySlugs = Category::query()
            ->where('type', Category::TYPE_NEWS)
            ->where('is_active', true)
            ->pluck('slug')
            ->flip();

        $menuTree = $this->buildTree($menus, null, $productCategorySlugs, $newsCategorySlugs);

        return array_merge([
            'menuTree' => $menuTree,
            'settings' => $settings,
        ], $data);
    }

    protected function buildTree(
        Collection $menus,
        ?int $parentId = null,
        Collection $productCategorySlugs = null,
        Collection $newsCategorySlugs = null
    ): Collection {
        $productCategorySlugs = $productCategorySlugs ?? collect();
        $newsCategorySlugs = $newsCategorySlugs ?? collect();

        return $menus
            ->where('parent_id', $parentId)
            ->values()
            ->map(function (Menu $menu) use ($menus, $productCategorySlugs, $newsCategorySlugs) {
                $menu->resolved_url = $this->resolveMenuUrl($menu, $productCategorySlugs, $newsCategorySlugs);
                $menu->children_tree = $this->buildTree(
                    $menus,
                    $menu->id,
                    $productCategorySlugs,
                    $newsCategorySlugs
                );

                return $menu;
            });
    }

    protected function resolveMenuUrl(
        Menu $menu,
        Collection $productCategorySlugs,
        Collection $newsCategorySlugs
    ): string {
        $slug = trim((string) $menu->slug, '/');

        if ($slug === '') {
            return route('frontend.home');
        }

        if ($slug === 'gioi-thieu') {
            return route('frontend.about');
        }

        if ($slug === 'lien-he') {
            return route('frontend.contact');
        }

        if ($slug === 'tin-tuc') {
            return route('frontend.news.index');
        }

        if ($productCategorySlugs->has($slug) || $newsCategorySlugs->has($slug)) {
            return route('frontend.categories.show', $slug);
        }

        return url('/' . $slug);
    }

    protected function collectDescendantIds(Collection $categories, int $parentId): array
    {
        $children = $categories->where('parent_id', $parentId);
        $ids = [$parentId];

        foreach ($children as $child) {
            $ids = array_merge($ids, $this->collectDescendantIds($categories, $child->id));
        }

        return array_values(array_unique($ids));
    }

    protected function buildCategoryTree(Collection $categories, ?int $parentId = null): Collection
    {
        return $categories
            ->where('parent_id', $parentId)
            ->values()
            ->map(function (Category $category) use ($categories) {
                $category->children_tree = $this->buildCategoryTree($categories, $category->id);

                return $category;
            });
    }

    protected function staticPageSeo(string $prefix, array $fallback = []): array
    {
        $seoConfig = SeoConfig::query()
            ->where('page_key', $prefix)
            ->first();

        return [
            'pageTitle' => $seoConfig?->title ?: ($fallback['title'] ?? ''),
            'pageDescription' => $seoConfig?->description ?: ($fallback['description'] ?? ''),
            'pageKeywords' => $seoConfig?->keywords ?: ($fallback['keywords'] ?? ''),
        ];
    }

    protected function attachPostCountsToCategoryTree(Collection $categoryTree, Collection $posts, string $attribute = 'posts_count'): Collection
    {
        return $categoryTree->map(function (Category $category) use ($posts, $attribute) {
            $children = $this->attachPostCountsToCategoryTree($category->children_tree ?? collect(), $posts, $attribute);
            $directCount = $posts->where('category_id', $category->id)->count();
            $childrenCount = $children->sum($attribute);

            $category->children_tree = $children;
            $category->{$attribute} = $directCount + $childrenCount;

            return $category;
        });
    }
}
