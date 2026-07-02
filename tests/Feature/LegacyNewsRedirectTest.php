<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyNewsRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_news_route_renders_when_canonical_url_matches_current_path()
    {
        $category = Category::query()->create([
            'type' => Category::TYPE_NEWS,
            'name' => 'Tin tuc',
            'slug' => 'tin-tuc',
            'is_active' => true,
        ]);

        $post = Post::query()->create([
            'type' => Post::TYPE_NEWS,
            'category_id' => $category->id,
            'title' => 'Bai viet tin tuc',
            'slug' => 'bai-viet-tin-tuc',
            'content' => '<p>Noi dung</p>',
            'is_active' => true,
        ]);

        $response = $this->get('/tin-tuc/' . $post->slug);

        $response->assertOk();
        $response->assertSee('Bai viet tin tuc');
    }
}
