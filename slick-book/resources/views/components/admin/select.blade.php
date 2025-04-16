@props(['label', 'name', 'options' => [], 'value' => ''])

<div class="mb-4">
    <label for="{{ $name }}" class="block font-semibold mb-1">{{ $label }}</label>
    <select name="{{ $name }}" id="{{ $name }}"
        class="w-100 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        {{ $attributes->merge(['class' => 'w-full border rounded px-3 py-2']) }}>
        @foreach ($options as $key => $label)
            @php
                $selectedValue = old($name, $value);
                if (is_array($selectedValue)) {
                    $selectedValue = reset($selectedValue); // 最初の要素を使う
                }
            @endphp
            <option value="{{ $key }}" @selected($selectedValue == $key) class="dark:text-white dark:bg-gray-800">
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>
