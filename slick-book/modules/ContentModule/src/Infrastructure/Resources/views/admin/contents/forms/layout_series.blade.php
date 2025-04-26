<div class="grid grid-cols-1 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">タイトル</label>
        <input type="text" name="title" value="{{ old('title', $entity?->getTitle() ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300" />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">スラッグ</label>
        <input type="text" name="slug" value="{{ old('slug', $entity?->getSlug() ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300" />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">説明 (body)</label>
        <textarea name="body" class="mt-1 block w-full rounded-md border-gray-300" rows="3">{{ old('body', $entity?->getBody() ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">並び順 (meta.order)</label>
        <input type="number" name="meta[order]" value="{{ old('meta.order', $entity?->getMeta()['order'] ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300" />
    </div>
</div>
