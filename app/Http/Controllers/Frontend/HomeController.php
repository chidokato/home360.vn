<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Post;

class HomeController extends BaseFrontendController
{
    public function index()
    {
        $seo = $this->staticPageSeo('home', [
            'title' => 'Trang chu',
            'description' => 'Trang chu bat dong san BDSVanPhuc.',
            'keywords' => 'trang chu, bat dong san, nha dat, du an, BDSVanPhuc',
        ]);

        $locationProjects = Post::query()
            ->select('province_id')
            ->selectRaw('COUNT(*) as projects_count')
            ->where('type', Post::TYPE_PRODUCT)
            ->where('is_active', true)
            ->whereNotNull('province_id')
            ->groupBy('province_id')
            ->orderByDesc('projects_count')
            ->limit(4)
            ->get();

        $representativeProjects = Post::query()
            ->with('province')
            ->where('type', Post::TYPE_PRODUCT)
            ->where('is_active', true)
            ->whereIn('province_id', $locationProjects->pluck('province_id')->filter())
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('province_id');

        $locationProjects = $locationProjects
            ->map(function ($locationProject) use ($representativeProjects) {
                $project = $representativeProjects->get($locationProject->province_id)?->first();

                if (! $project || ! $project->province) {
                    return null;
                }

                return (object) [
                    'name' => $project->province->name,
                    'projects_count' => (int) $locationProject->projects_count,
                    'image' => $project->location_image ?: $project->image,
                    'frontend_url' => $project->frontend_url,
                ];
            })
            ->filter()
            ->values();

        return view('frontend.home', $this->sharedViewData([
            'featuredProducts' => Post::query()
                ->with(['category', 'galleryImages'])
                ->where('type', Post::TYPE_PRODUCT)
                ->where('is_active', true)
                ->where('is_featured', true)
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(3)
                ->get(),
            'latestProducts' => Post::query()
                ->with(['category', 'galleryImages'])
                ->where('type', Post::TYPE_PRODUCT)
                ->where('is_active', true)
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(10)
                ->get(),
            'latestNews' => Post::query()
                ->with('category')
                ->where('type', Post::TYPE_NEWS)
                ->where('is_active', true)
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(3)
                ->get(),
            'locationProjects' => $locationProjects,
            'apartments' => \App\Models\Apartment::query()
                ->with(['images', 'project'])
                ->where('is_active', true)
                ->orderByDesc('id')
                ->limit(10)
                ->get(),
        ] + $seo));
    }
}
