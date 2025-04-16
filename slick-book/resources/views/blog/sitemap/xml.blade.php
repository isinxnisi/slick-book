<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ $site->site_url }}</loc>
        <priority>1.0</priority>
    </url>

    @foreach($posts as $post)
        <url>
            <loc>{{ route('posts.view', $post) }}</loc>
            <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
        </url>
    @endforeach

    @foreach($categories as $category)
        <url>
            <loc>{{ route('blog.category', $category->slug) }}</loc>
        </url>
    @endforeach

    @foreach($tags as $tag)
        <url>
            <loc>{{ route('blog.tag', $tag->slug) }}</loc>
        </url>
    @endforeach

    @foreach($tagGroups as $group)
        <url>
            <loc>{{ route('blog.tagGroup', $group->slug) }}</loc>
        </url>
    @endforeach
</urlset>
