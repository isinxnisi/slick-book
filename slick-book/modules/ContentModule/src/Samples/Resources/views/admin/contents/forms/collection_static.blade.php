{{-- collection.static 用カスタムフォーム --}}
@include('content-module::admin.contents.forms._base')

@php
    $type = $entity?->getContentType() ?? '';
    $kind = $entity?->getContentKind() ?? '';
    $isEdit = isset($entity) && $entity->getId();
@endphp
<section class="mb-8">
    <h2 class="text-xl font-semibold mb-4">コレクション情報（Static）</h2>

    {{-- target_type --}}
    <div class="form-group mb-4">
        <label for="meta_target_type">コレクション対象</label>
        <select id="meta_target_type" name="meta[target_type]" class="form-control" {{ $isEdit ? 'disabled' : '' }}>
            @foreach ($sections[0]['fields'][0]['options'] as $val => $lbl)
                <option value="{{ $val }}" @selected(old('meta.target_type') == $val)>
                    {{ $lbl }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- item_ids --}}
    <div class="form-group mb-4">
        <label for="meta_item_ids">項目選択</label>
        <select id="meta_item_ids" name="meta[item_ids][]" multiple class="form-control">
            {{-- JS でオプション注入 --}}
        </select>
    </div>
</section>

<script>
    const initialSelected = @json(old(
        'meta.item_ids',
        $entity->getMetaValue('item_ids', [])
    ));
    (async () => {
        const target = document.getElementById('meta_target_type');
        const items = document.getElementById('meta_item_ids');

        async function loadOptions() {
            const url = new URL("{{ route('admin.contents.items') }}", location.origin);
            url.searchParams.set('type', '{{ $type }}');
            url.searchParams.set('kind', '{{ $kind }}');
            url.searchParams.set('target_key', target.value);

            const resp = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await resp.json();
            items.innerHTML = '';
            for (const [value, label] of Object.entries(data[0])) {
                const opt = document.createElement('option');
                opt.value = value;
                opt.textContent = label;
                if (initialSelected != null && initialSelected.includes(value)) {
                    opt.selected = true;
                }
                items.append(opt);
            }
        }

        target.addEventListener('change', loadOptions);
        loadOptions();
    })();
</script>
