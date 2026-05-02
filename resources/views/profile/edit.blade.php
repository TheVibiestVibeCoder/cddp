<x-app-layout>
    <x-slot name="title">Profile Settings</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-ink-950 tracking-tight">Profile Settings</h1>
        <p class="text-sm text-ink-500 mt-0.5">Manage your account information and password</p>
    </div>

    <div class="max-w-2xl space-y-6">
        <div class="card p-6">
            @include('profile.partials.update-profile-information-form')
        </div>
        <div class="card p-6">
            @include('profile.partials.update-password-form')
        </div>
        <div class="card p-6 border-red-200">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
