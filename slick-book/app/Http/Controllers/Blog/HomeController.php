<?php
namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Post;

class HomeController extends Controller
{
    /**
     * Home
     */
    public function index()
    {
        $site = app('CurrentSite');

        $posts = Post::where('status', 'published')
            ->where('site_id', $site->id)
            ->where('is_deleted', false)
            ->paginate(10);

        return view('blog.home', compact('posts'));
    }

}
