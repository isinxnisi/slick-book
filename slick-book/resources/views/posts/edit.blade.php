@section('title', '投稿の編集')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('投稿の編集') }}
        </h2>
    </x-slot>

    <div class="mx-auto sm:px-6 lg:px-8">
        <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                <form action="{{ route('posts.update', $post->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <label>タイトル:</label>
                    <x-text-input name="title" type="text" class="mt-1 block w-full" :value="old('title', $post->title)" required autofocus autocomplete="title" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />

                    <label>本文 (Markdown):</label>
                    <x-textarea name="body" class="mt-1 block w-full" required autofocus autocomplete="body">
                        {{ old('body', $post->body) }}
                    </x-textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('body')" />

                    <label>公開状態:</label>
                    <select name="status"
                        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 
                            dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm p-2 w-full">
                        <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>下書き</option>
                        <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>公開</option>
                    </select>

                    <x-input-error class="mt-2" :messages="$errors->get('status')" />

                    <div class="row">
                        <div class="flex col-6 justify-start mt-4">
                            <button type="button" onclick="history.back()" class="px-4 py-2 bg-gray-500 text-white rounded-md shadow-sm hover:bg-gray-600">
                                {{ __('戻る') }}
                            </button>
                        </div>
                        <div class="flex col-6 justify-end mt-4">
                            <x-primary-button>
                                {{ __('投稿する') }}
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>