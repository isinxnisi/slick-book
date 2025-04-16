@props(['label' => null, 'name' => null, 'disabled' => false])

<div class="mb-4">
    @isset($label)
    <label for="{{ $name }}" class="block font-bold mb-1">{{ $label }}</label>
    @endisset
    <textarea
        name="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500
                            dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm p-2 w-full',
        ]) }}
        @disabled($disabled)>{{ $slot }}</textarea>
</div>
