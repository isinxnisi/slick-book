@props(['type' => 'submit'])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700']) }}
    class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
    {{ $slot }}
</button>
