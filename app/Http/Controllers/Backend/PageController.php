<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::query()
            ->latest()
            ->paginate(10);

        return view('backend.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('backend.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePage($request);

        Page::create($this->payload($request, $validated));

        return redirect()
            ->route('backend.pages.index')
            ->with('success', 'Them page thanh cong.');
    }

    public function edit(Page $page)
    {
        return view('backend.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $this->validatePage($request, $page->id);

        $page->update($this->payload($request, $validated, $page));

        if ($request->boolean('save_stay')) {
            return redirect()
                ->route('backend.pages.edit', $page)
                ->with('success', 'Cap nhat page thanh cong.');
        }

        return redirect()
            ->route('backend.pages.index')
            ->with('success', 'Cap nhat page thanh cong.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()
            ->route('backend.pages.index')
            ->with('success', 'Xoa page thanh cong.');
    }

    public function toggleStatus(Page $page)
    {
        $page->update([
            'is_active' => ! $page->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cap nhat trang thai page thanh cong.',
            'is_active' => $page->is_active,
            'label' => $page->is_active ? 'Hien thi' : 'An',
        ]);
    }

    protected function validatePage(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('pages', 'slug')->ignore($ignoreId),
            ],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'html_content' => ['nullable', 'string'],
            'html_file' => ['nullable', 'file', 'mimes:html,htm', 'max:10240'],
            'published_at' => ['nullable', 'date'],
            'is_active' => ['nullable'],
        ]);
    }

    protected function payload(Request $request, array $validated, ?Page $page = null): array
    {
        $htmlContent = $validated['html_content'] ?? $page?->html_content;

        if ($request->hasFile('html_file')) {
            $htmlContent = file_get_contents($request->file('html_file')->getRealPath());
        }

        return [
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?: Str::slug($validated['title']),
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'html_content' => $htmlContent,
            'is_active' => $request->boolean('is_active'),
            'published_at' => $validated['published_at'] ?? null,
        ];
    }
}
