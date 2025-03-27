<?php

namespace App\Http\Controllers\Admin;

use App\Services\TagGroupService;
use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteTagGroup;
use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SiteTagGroupController extends Controller
{
    public function __construct(protected TagGroupService $tagGroupService) {}

    public function index(Request $request)
    {
        $siteId = $request->input('site', Site::first()?->id);
        $sites = Site::all();
    
        // サイトに紐づくタググループ（左UI）
        $groups = new TagGroup()->getSiteTagGroupTree($siteId);
    
        // マスタグループ（右UI）取得 → flatten & purpose注入
        $mastaGroups = TagGroup::whereNull('parent_id')
            ->whereNotIn('id', function ($query) {
                $query->select('tag_group_id')->from('site_tag_group');
            })
            ->with([
                'parent',
                'tags' => fn ($q) => $q->orderBy('name'),
                'children.parent',
                'children.tags',
                'children.children.parent',
                'children.children.tags',
            ])
            ->orderBy('order')
            ->get();
    
        $mastaGroups = $this->tagGroupService->flattenGroups($mastaGroups);
        $this->tagGroupService->injectPurposeIntoTags($groups);
    
        $purpose = request()->get('purpose', 'public');
    
        $selectedTagIds = TagGroup::with(['tags:id'])
            ->whereIn('id', function ($query) use ($siteId) {
                $query->select('tag_group_id')
                    ->from('site_tag_group')
                    ->where('site_id', $siteId);
            })
            ->get()
            ->mapWithKeys(function ($group) {
                return [$group->id => $group->tags->pluck('id')->toArray()];
            });
    
        return view('admin.site-tag-groups.index', compact(
            'sites',
            'siteId',
            'groups',
            'mastaGroups',
            'purpose',
            'selectedTagIds'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tag_groups',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:tag_groups,id',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'], '-', 'ja');
        }

        $maxOrder = TagGroup::where('parent_id', $validated['parent_id'] ?? null)->max('order');
        $validated['order'] = is_null($maxOrder) ? 1 : $maxOrder + 1;

        $tagGroup = TagGroup::create($validated);

        SiteTagGroup::create([
            'site_id' => $validated['site_id'],
            'tag_group_id' => $tagGroup->id,
        ]);

        return response()->json(['message' => '作成しました'], 201);
    }

    public function update(Request $request, TagGroup $tagGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tag_groups,slug,' . $tagGroup->id,
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
        ]);

        $tagGroup->update($validated);

        return response()->json(['message' => '更新しました']);
    }

    public function destroy(TagGroup $tagGroup)
    {
        // サイトとの紐づけも削除（cascadeで自動なら不要）
        SiteTagGroup::where('tag_group_id', $tagGroup->id)->delete();

        $tagGroup->delete();
        return response()->json(['message' => '削除しました']);
    }

    public function reorder(Request $request)
    {
        foreach ($request->input('hierarchy', []) as $node) {
            $this->updateGroupOrder($node, null);
        }

        return response()->json(['message' => '並び順を更新しました']);
    }

    public function getMasterTags(Request $request)
    {
        $siteId = $request->input('site', Site::first()?->id);
        $purpose = $request->get('purpose', 'public');
    
        $mastaGroups = TagGroup::whereNull('parent_id')
            ->whereNotIn('id', function ($query) {
                $query->select('tag_group_id')->from('site_tag_group');
            })
            ->with([
                'parent',
                'tags' => fn ($q) => $q->orderBy('name'),
                'children.parent',
                'children.tags',
                'children.children.parent',
                'children.children.tags',
            ])
            ->orderBy('order')
            ->get();
    
        $mastaGroups = $this->tagGroupService->flattenGroups($mastaGroups);
        $this->tagGroupService->injectPurposeIntoTags($mastaGroups);
    
        $selectedTagIds = TagGroup::with(['tags:id'])
            ->whereIn('id', function ($query) use ($siteId) {
                $query->select('tag_group_id')
                    ->from('site_tag_group')
                    ->where('site_id', $siteId);
            })
            ->get()
            ->mapWithKeys(function ($group) {
                return [$group->id => $group->tags->pluck('id')->toArray()];
            });
    
        return view('components.admin.tags.tag-selection', [
            'groups' => $mastaGroups,
            'selectedTagIds' => $selectedTagIds,
            'purpose' => $purpose,
            'siteId' => $siteId,
        ])->render();
    }

    protected function updateGroupOrder(array $node, $parentId)
    {
        static $order = 1;

        TagGroup::where('id', $node['id'])->update([
            'parent_id' => $parentId,
            'order' => $order++,
        ]);

        foreach ($node['children'] ?? [] as $child) {
            $this->updateGroupOrder($child, $node['id']);
        }
    }

    protected function flattenGroups($groups)
    {
        $flattened = collect();
    
        foreach ($groups as $group) {
            $flattened->push($group);
    
            if ($group->children) {
                $flattened = $flattened->merge($this->flattenGroups($group->children));
            }
        }
    
        return $flattened;
    }

    protected function injectPurposeIntoTags($groups)
    {
        foreach ($groups as $group) {
            foreach ($group->tags as $tag) {
                $firstMasterGroup = $tag->tagGroups->first();
                $tag->purpose = $firstMasterGroup?->purpose ?? 'public';
            }
    
            // 再帰的に子グループにも適用
            if ($group->children && $group->children->isNotEmpty()) {
                $this->injectPurposeIntoTags($group->children);
            }
        }
    }
}
