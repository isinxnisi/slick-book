@props([
    'tabs' => [],
    'active' => '',
])

<div class="flex space-x-2 border-b border-gray-700">
    @foreach ($tabs as $key => $label)
        <button
            type="button"
            class="tab-button px-3 py-1 text-sm font-medium rounded-t 
                {{ $active === $key ? 'bg-gray-800 text-white border-t border-l border-r border-indigo-500' : 'text-gray-400 hover:text-white' }}"
            data-tab="{{ $key }}"
        >
            {{ $label }}
        </button>
    @endforeach
</div>

@push('scripts')
<script>
    $(function () {
        $('.tab-button').on('click', function () {
            const tab = $(this).data('tab');
            $('.tab-button').removeClass('bg-gray-800 text-white border-indigo-500').addClass('text-gray-400');
            $(this).addClass('bg-gray-800 text-white border-indigo-500');

            $('.tab-content').addClass('hidden');
            $('#panel-tab-' + tab).removeClass('hidden');
        });
    });
</script>
@endpush
