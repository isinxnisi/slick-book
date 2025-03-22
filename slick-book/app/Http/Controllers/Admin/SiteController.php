<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::all();
        return view('admin.sites.index', compact('sites'));
    }

    public function create()
    {
        return view('admin.sites.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sites,slug',
            'description' => 'nullable|string',
        ]);

        Site::create($validated);
        return redirect()->route('sites.index')->with('success', 'サイトを作成しました');
    }

    public function edit(Site $site)
    {
        return view('admin.sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sites,slug,' . $site->id,
            'description' => 'nullable|string',
        ]);

        $site->update($validated);
        return redirect()->route('sites.index')->with('success', 'サイトを更新しました');
    }

    public function destroy(Site $site)
    {
        $site->delete(); // ソフトデリートを使わないなら削除のみ
        return redirect()->route('sites.index')->with('success', 'サイトを削除しました');
    }
}
