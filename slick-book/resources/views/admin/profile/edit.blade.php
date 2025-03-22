@section('title', 'Profile')
<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="max-w-4xl space-y-6">
        <div class="p-4 sm:p-8 dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('admin.profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('admin.profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('admin.profile.partials.delete-user-form')
            </div>
        </div>
    </div>

</x-app-layout>
