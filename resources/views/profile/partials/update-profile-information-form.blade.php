<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Avatar -->
        <div x-data="{ mode: 'file' }">
            <x-input-label :value="__('Profile Picture')" />
            @if($user->avatar)
            <div class="mt-2 mb-3 flex items-center gap-3">
                <img src="{{ $user->avatar_url }}" class="w-14 h-14 rounded-full object-cover border border-gray-200" alt="">
                <span class="text-sm text-gray-500">Current picture</span>
            </div>
            @endif
            <div class="flex gap-3 mb-2">
                <button type="button" @click="mode = 'file'"
                        :class="mode === 'file' ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                        class="px-3 py-1.5 text-xs rounded-md font-medium transition-colors">Upload file</button>
                <button type="button" @click="mode = 'url'"
                        :class="mode === 'url' ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                        class="px-3 py-1.5 text-xs rounded-md font-medium transition-colors">Paste URL</button>
            </div>
            <div x-show="mode === 'file'">
                <input type="file" name="avatar_file" accept="image/*"
                       class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                <p class="mt-1 text-xs text-gray-500">Max 5MB &middot; JPG, PNG, GIF, WebP</p>
            </div>
            <div x-show="mode === 'url'" x-cloak>
                <input type="url" name="avatar_url" value="{{ old('avatar_url') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="https://…">
            </div>
            @error('avatar_file')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            @error('avatar_url')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
