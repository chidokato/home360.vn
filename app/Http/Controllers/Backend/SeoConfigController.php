<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SeoConfig;
use Illuminate\Http\Request;

class SeoConfigController extends Controller
{
    protected const STATIC_PAGES = [
        'home' => 'Trang chu',
        'about' => 'Gioi thieu',
        'contact' => 'Lien he',
    ];

    public function edit()
    {
        $seoConfigs = SeoConfig::query()
            ->whereIn('page_key', array_keys(self::STATIC_PAGES))
            ->get()
            ->keyBy('page_key');

        foreach (self::STATIC_PAGES as $pageKey => $label) {
            if (! $seoConfigs->has($pageKey)) {
                $seoConfigs->put($pageKey, new SeoConfig([
                    'page_key' => $pageKey,
                ]));
            }
        }

        $pages = self::STATIC_PAGES;

        return view('backend.seo.edit', compact('seoConfigs', 'pages'));
    }

    public function update(Request $request)
    {
        $rules = [];

        foreach (array_keys(self::STATIC_PAGES) as $pageKey) {
            $rules[$pageKey . '.title'] = ['nullable', 'string', 'max:255'];
            $rules[$pageKey . '.description'] = ['nullable', 'string'];
            $rules[$pageKey . '.keywords'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

        foreach (array_keys(self::STATIC_PAGES) as $pageKey) {
            $payload = $validated[$pageKey] ?? [];

            SeoConfig::query()->updateOrCreate(
                ['page_key' => $pageKey],
                [
                    'title' => $payload['title'] ?? null,
                    'description' => $payload['description'] ?? null,
                    'keywords' => $payload['keywords'] ?? null,
                ]
            );
        }

        return redirect()
            ->route('backend.seo.edit')
            ->with('success', 'Cap nhat SEO thanh cong.');
    }
}
