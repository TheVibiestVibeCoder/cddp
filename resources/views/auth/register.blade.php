<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-ink-950 tracking-tight mb-1">Create an account</h1>
        <p class="text-sm text-ink-500">Join the CDDP research platform</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="label">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   class="input @error('name') input-error @enderror"
                   required autofocus autocomplete="name" placeholder="Your name">
            @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="input @error('email') input-error @enderror"
                   required autocomplete="username" placeholder="you@example.com">
            @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="organization" class="label">Organisation <span class="text-ink-400 normal-case tracking-normal font-normal">(optional)</span></label>
            <input id="organization" type="text" name="organization" value="{{ old('organization') }}"
                   class="input" placeholder="e.g. Research Institute">
        </div>

        <div>
            <label for="password" class="label">Password</label>
            <input id="password" type="password" name="password"
                   class="input @error('password') input-error @enderror"
                   required autocomplete="new-password" placeholder="Min. 8 characters">
            @error('password')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="input" required autocomplete="new-password" placeholder="Repeat password">
        </div>

        <button type="submit" class="btn-primary w-full justify-center py-3">
            Create account
        </button>

        <p class="text-center text-sm text-ink-500">
            Already have an account?
            <a href="{{ route('login') }}" class="text-ink-950 font-medium hover:underline">Sign in</a>
        </p>
    </form>
</x-guest-layout>
