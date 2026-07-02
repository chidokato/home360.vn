<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;

class PageController extends BaseFrontendController
{
    public function productCategory(string $slug)
    {
        $sort = (string) request()->query('sort', '');

        $category = Category::query()
            ->where('type', Category::TYPE_PRODUCT)
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        $allCategories = Category::query()
            ->where('type', Category::TYPE_PRODUCT)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categoryIds = $this->collectDescendantIds($allCategories, $category->id);
        $categoryTree = $this->buildCategoryTree($allCategories);

        $productsQuery = Post::query()
            ->with('category')
            ->where('type', Post::TYPE_PRODUCT)
            ->where('is_active', true)
            ->whereIn('category_id', $categoryIds);

        switch ($sort) {
            case 'old':
                $productsQuery
                    ->orderBy('published_at')
                    ->orderBy('id');
                break;
            case 'default':
            case 'new':
            default:
                $productsQuery
                    ->orderByDesc('published_at')
                    ->orderByDesc('id');
                break;
        }

        $products = $productsQuery->get();

        return view('frontend.products.index', $this->sharedViewData([
            'category' => $category,
            'categoryTree' => $categoryTree,
            'products' => $products,
            'currentSort' => $sort,
            'pageTitle' => $category->seo_title ?: $category->name,
            'pageDescription' => $category->seo_description,
        ]));
    }

    public function categoryBySlug(string $slug)
    {
        $category = Category::query()
            ->where('is_active', true)
            ->where('slug', $slug)
            ->first();

        if (! $category) {
            $page = Page::query()
                ->where('is_active', true)
                ->where('slug', $slug)
                ->firstOrFail();

            return $this->renderPage($page);
        }

        if ($category->type === Category::TYPE_PRODUCT) {
            return $this->productCategory($slug);
        }

        return $this->newsCategory($slug);
    }

    public function legacyProductShow(string $slug)
    {
        $product = Post::query()
            ->with(['category', 'province', 'ward.province', 'galleryImages', 'floorPlans', 'apartments.images'])
            ->where('type', Post::TYPE_PRODUCT)
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        if (! $this->shouldRedirectToCanonical($product->frontend_url)) {
            return $this->renderProductShow($product);
        }

        return redirect()->to($product->frontend_url, $this->canonicalRedirectStatus());
    }

    public function legacyNewsShow(string $slug)
    {
        $post = Post::query()
            ->with('category')
            ->where('type', Post::TYPE_NEWS)
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        if (! $this->shouldRedirectToCanonical($post->frontend_url)) {
            return $this->renderNewsShow($post);
        }

        return redirect()->to($post->frontend_url, $this->canonicalRedirectStatus());
    }

    public function contentShow(string $categorySlug, string $slug)
    {
        $category = Category::query()
            ->where('is_active', true)
            ->where('slug', $categorySlug)
            ->firstOrFail();

        if ($category->type === Category::TYPE_PRODUCT) {
            $product = Post::query()
                ->with(['category', 'province', 'ward.province', 'galleryImages', 'seller', 'floorPlans', 'apartments.images'])
                ->where('type', Post::TYPE_PRODUCT)
                ->where('is_active', true)
                ->where('category_id', $category->id)
                ->where('slug', $slug)
                ->firstOrFail();

            return $this->renderProductShow($product);
        }

        $post = Post::query()
            ->with('category')
            ->where('type', Post::TYPE_NEWS)
            ->where('is_active', true)
            ->where('category_id', $category->id)
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->renderNewsShow($post);
    }

    protected function renderProductShow(Post $product)
    {
        $contactSeller = User::query()
            ->orderBy('id')
            ->first();

        if ($product->relationLoaded('seller') && $product->seller) {
            $contactSeller = $product->seller;
        }

        $relatedProducts = Post::query()
            ->with('category')
            ->where('type', Post::TYPE_PRODUCT)
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, function ($query) use ($product) {
                $query->where('category_id', $product->category_id);
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        return view('frontend.products.show', $this->sharedViewData([
            'product' => $product,
            'contactSeller' => $contactSeller,
            'relatedProducts' => $relatedProducts,
            'pageTitle' => $product->seo_title ?: $product->title,
            'pageDescription' => $product->seo_description ?: $product->summary,
        ]));
    }

    public function newsIndex()
    {
        $categories = Category::query()
            ->where('type', Category::TYPE_NEWS)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categoryTree = $this->buildCategoryTree($categories);

        $posts = Post::query()
            ->with('category')
            ->where('type', Post::TYPE_NEWS)
            ->where('is_active', true)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();
        $categoryTree = $this->attachPostCountsToCategoryTree($categoryTree, $posts, 'news_posts_count');

        return view('frontend.news.index', $this->sharedViewData([
            'currentCategory' => null,
            'categories' => $categoryTree,
            'posts' => $posts,
            'recentPosts' => $posts->take(5),
            'pageTitle' => 'Tin tuc',
            'pageDescription' => 'Tin tuc va bai viet moi nhat.',
        ]));
    }

    public function newsCategory(string $slug)
    {
        $category = Category::query()
            ->where('type', Category::TYPE_NEWS)
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        $allCategories = Category::query()
            ->where('type', Category::TYPE_NEWS)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categoryIds = $this->collectDescendantIds($allCategories, $category->id);
        $categoryTree = $this->buildCategoryTree($allCategories);

        $posts = Post::query()
            ->with('category')
            ->where('type', Post::TYPE_NEWS)
            ->where('is_active', true)
            ->whereIn('category_id', $categoryIds)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();
        $allNewsPosts = Post::query()
            ->where('type', Post::TYPE_NEWS)
            ->where('is_active', true)
            ->get(['id', 'category_id']);
        $categoryTree = $this->attachPostCountsToCategoryTree($categoryTree, $allNewsPosts, 'news_posts_count');

        return view('frontend.news.index', $this->sharedViewData([
            'currentCategory' => $category,
            'categories' => $categoryTree,
            'posts' => $posts,
            'recentPosts' => $posts->take(5),
            'pageTitle' => $category->seo_title ?: $category->name,
            'pageDescription' => $category->seo_description,
        ]));
    }

    protected function renderNewsShow(Post $post)
    {
        $relatedPosts = Post::query()
            ->with('category')
            ->where('type', Post::TYPE_NEWS)
            ->where('is_active', true)
            ->where('id', '!=', $post->id)
            ->when($post->category_id, function ($query) use ($post) {
                $query->where('category_id', $post->category_id);
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        $categories = Category::query()
            ->where('type', Category::TYPE_NEWS)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $categoryTree = $this->buildCategoryTree($categories);

        $recentPosts = Post::query()
            ->with('category')
            ->where('type', Post::TYPE_NEWS)
            ->where('is_active', true)
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();
        $allNewsPosts = Post::query()
            ->where('type', Post::TYPE_NEWS)
            ->where('is_active', true)
            ->get(['id', 'category_id']);
        $categoryTree = $this->attachPostCountsToCategoryTree($categoryTree, $allNewsPosts, 'news_posts_count');

        return view('frontend.news.show', $this->sharedViewData([
            'post' => $post,
            'categories' => $categoryTree,
            'recentPosts' => $recentPosts,
            'relatedPosts' => $relatedPosts,
            'pageTitle' => $post->seo_title ?: $post->title,
            'pageDescription' => $post->seo_description ?: $post->summary,
        ]));
    }

    protected function renderPage(Page $page)
    {
        $htmlContent = $page->html_content ?? '';

        if (Str::contains(Str::lower($htmlContent), ['<!doctype', '<html'])) {
            return response($htmlContent);
        }

        return view('frontend.pages.show', $this->sharedViewData([
            'page' => $page,
            'pageTitle' => $page->seo_title ?: $page->title,
            'pageDescription' => $page->seo_description,
        ]));
    }

    public function about()
    {
        return view('frontend.pages.about', $this->sharedViewData(
            $this->staticPageSeo('about', [
                'title' => 'Gioi thieu',
                'description' => 'Thong tin gioi thieu ve cong ty.',
                'keywords' => 'gioi thieu, cong ty, BDSVanPhuc',
            ])
        ));
    }

    public function contact()
    {
        return view('frontend.pages.contact', $this->sharedViewData(
            $this->staticPageSeo('contact', [
                'title' => 'Lien he',
                'description' => 'Thong tin lien he voi chung toi.',
                'keywords' => 'lien he, hotline, dia chi, BDSVanPhuc',
            ])
        ));
    }

    protected function shouldRedirectToCanonical(string $canonicalUrl): bool
    {
        $currentUrl = url()->current();

        return rtrim($canonicalUrl, '/') !== rtrim($currentUrl, '/');
    }

    protected function canonicalRedirectStatus(): int
    {
        return app()->environment('local') ? 302 : 301;
    }
}
