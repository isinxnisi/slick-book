<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * サイト管理 Controller class
 */
class SiteController extends Controller
{
    /**
     * サイト管理｜一覧画面
     *
     * @return View
     */
    public function index()
    {
        $sites = Site::orderBy('id')->get();
        return view('admin.sites.index', compact('sites'));
    }

    /**
     * サイト管理｜登録画面
     *
     * @return View
     */
    public function create()
    {
        return view('admin.sites.create');
    }

    /**
     * サイト管理｜登録処理
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sites,slug',
            'domain' => 'nullable|string|max:255|unique:sites,domain',
            'description' => 'nullable|string',
        ]);

        Site::create($validated);
        return redirect()->route('sites.index')->with('success', 'サイトを作成しました');
    }

    /**
     * サイト管理｜更新画面
     *
     * @param Site $site
     * @return View
     */
    public function edit(Site $site)
    {
        return view('admin.sites.edit', compact('site'));
    }

    /**
     * サイト管理｜更新処理
     *
     * @param Request $request
     * @param Site $site
     * @return RedirectResponse
     */
    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sites,slug,' . $site->id,
            'domain' => 'nullable|string|max:255|unique:sites,domain,' . $site->id,
            'description' => 'nullable|string',
        ]);

        $site->update($validated);
        return redirect()->route('sites.index')->with('success', 'サイトを更新しました');
    }

    /**
     * サイト管理｜削除処理
     *
     * @param Site $site
     * @return RedirectResponse
     */
    public function destroy(Site $site)
    {
        $site->delete(); // ソフトデリートを使わないなら削除のみ
        return redirect()->route('sites.index')->with('success', 'サイトを削除しました');
    }
}
