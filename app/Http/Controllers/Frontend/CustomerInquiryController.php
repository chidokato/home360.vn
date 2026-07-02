<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerInquiry;
use App\Models\Post;
use Illuminate\Http\Request;

class CustomerInquiryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validateWithBag('customerInquiry', [
            'post_id' => ['nullable', 'integer', 'exists:posts,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'source_url' => ['nullable', 'url', 'max:2048'],
            'download_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $post = null;
        $sourceUrl = $this->resolveSourceUrl($validated['source_url'] ?? null);
        $downloadUrl = null;

        if (! empty($validated['post_id'])) {
            $post = Post::query()->find($validated['post_id']);
            $downloadUrl = $this->resolveDownloadUrl($post);
        }

        CustomerInquiry::create([
            'post_id' => $post?->id,
            'project_title' => $post?->title,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'source_url' => $sourceUrl,
            'download_url' => $downloadUrl,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $redirect = redirect()
            ->to($sourceUrl ?: url('/'))
            ->with('customer_inquiry_success', 'Thong tin cua ban da duoc ghi nhan. Chung toi se lien he voi ban som nhat.');

        if ($downloadUrl) {
            $redirect->with('customer_inquiry_download_url', $downloadUrl);
        }

        return $redirect;
    }

    protected function resolveSourceUrl(?string $sourceUrl): string
    {
        if (! $sourceUrl) {
            return url('/');
        }

        $sourceHost = parse_url($sourceUrl, PHP_URL_HOST);
        $appHost = parse_url(url('/'), PHP_URL_HOST);

        if ($sourceHost && $appHost && $sourceHost === $appHost) {
            return $sourceUrl;
        }

        return url('/');
    }

    protected function resolveDownloadUrl(?Post $post): ?string
    {
        if (! $post) {
            return null;
        }

        return collect([
            data_get($post, 'price_list_url'),
            data_get($post, 'brochure_url'),
            data_get($post, 'download_url'),
            data_get($post, 'attachment'),
            data_get($post, 'file'),
        ])->filter(fn ($value) => filled($value))
            ->map(function ($value) {
                $value = trim((string) $value);

                if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                    return $value;
                }

                return asset(ltrim($value, '/'));
            })
            ->first();
    }
}
