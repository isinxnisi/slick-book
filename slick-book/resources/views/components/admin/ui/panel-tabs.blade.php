@props([
    'tabs' => [],
    'active' => '',
])

<div class="tab flex space-x-2 border-b border-gray-700" style="margin-bottom: -1px;">
    @foreach ($tabs as $key => $label)
        <button
            type="button"
            class="tab-button relative px-4 py-2 text-sm font-medium transition-all hover:text-white hover:border-b-2
                {{ $active === $key ? 'text-white border-b-2 border-indigo-500 hover:border-indigo-500' : 'text-gray-400 hover:border-gray-500' }}"
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
            $('.tab-button').removeClass('text-white border-b-2 border-indigo-500').addClass('text-gray-400 hover:border-gray-500');
            $(this).removeClass('text-gray-400 hover:border-gray-500').addClass('text-white border-b-2 border-indigo-500 hover:border-indigo-500');

            $('.tab-content').addClass('hidden');
            $('#panel-tab-' + tab).removeClass('hidden');
        });
    });
</script>
@endpush
