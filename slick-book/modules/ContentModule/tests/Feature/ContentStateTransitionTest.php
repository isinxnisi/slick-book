<?php

// tests/Feature/ContentStateTransitionTest.php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ContentStateTransitionTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        // 必要ならパッケージのマイグレーションを手動ロード
        $this->artisan('migrate', ['--path' => 'modules/ContentModule/database/migrations/package']);
    }

    public function test_to_review_changes_status()
    {
        // 下書き状態のレコードを直接DB登録
        $id = DB::table('contents')->insertGetId([
            'scope_key'    => null,
            'title'        => 'テスト記事',
            'slug'         => 'test-article',
            'content_type' => 'slot',
            'content_kind' => 'article',
            'body'         => '本文',
            'meta'         => json_encode([]),
            'status'       => 'draft',
            'published_at' => null,
            'created_by'   => null,
            'updated_by'   => null,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // エンドポイント呼び出し
        $response = $this->post(route('admin.contents.to-review', $id));
        $response->assertRedirect();

        // ステータスが review に変わっていること
        $this->assertDatabaseHas('contents', [
            'id'     => $id,
            'status' => 'review',
        ]);
    }

    public function test_publish_changes_status()
    {
        $id = DB::table('contents')->insertGetId([
            'scope_key'    => null,
            'title'        => 'テスト記事',
            'slug'         => 'test-article-2',
            'content_type' => 'slot',
            'content_kind' => 'article',
            'body'         => '本文',
            'meta'         => json_encode([]),
            'status'       => 'review',
            'published_at' => null,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $response = $this->post(route('admin.contents.publish', $id));
        $response->assertRedirect();

        $this->assertDatabaseHas('contents', [
            'id'     => $id,
            'status' => 'published',
        ]);
    }

    public function test_archive_changes_status()
    {
        $id = DB::table('contents')->insertGetId([
            'scope_key'    => null,
            'title'        => 'テスト記事',
            'slug'         => 'test-article-3',
            'content_type' => 'slot',
            'content_kind' => 'article',
            'body'         => '本文',
            'meta'         => json_encode([]),
            'status'       => 'published',
            'published_at' => now(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $response = $this->post(route('admin.contents.archive', $id));
        $response->assertRedirect();

        $this->assertDatabaseHas('contents', [
            'id'     => $id,
            'status' => 'archived',
        ]);
    }
}

