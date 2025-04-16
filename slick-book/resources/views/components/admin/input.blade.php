@props(['label', 'name', 'value' => '', 'type' => 'text'])

<div class="mb-4">
    <label for="{{ $name }}" class="block font-bold mb-1">{{ $label }}</label>
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        class="w-100 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        {{ $attributes->merge(['class' => 'w-full border rounded px-3 py-2']) }}
    >
</div>
