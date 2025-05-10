<?php

namespace Modules\ContentModule\Samples\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\ContentModule\Core\Application\Services\ContentService;

class ItemController extends Controller
{
    public function __construct(private ContentService $service) {}

    /**
     * 任意の TYPE×KIND 戦略からアイテム一覧を返す
     */
    public function items(Request $request): JsonResponse
    {
        $params = $request->validate([
            'type'        => ['required', 'string'],
            'kind'        => ['required', 'string'],
            'target_key'  => ['required', 'string'],
        ]);

        $strategy = $this->service->resolveStrategy($params['type'], $params['kind']);

        // ItemProviderInterface を実装していない戦略には空配列
        if (! $strategy instanceof \Modules\ContentModule\Core\Application\Strategies\ItemProviderInterface) {
            return response()->json([]);
        }

        $items = $strategy->getItems(
            $params['target_key'],
            ['type' => $params['type'], 'kind' => $params['kind']]
        );

        return response()->json($items);
    }
}
