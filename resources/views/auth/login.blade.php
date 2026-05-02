<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-ink-950 tracking-tight mb-1">Welcome back</h1>
        <p class="text-sm text-ink-500">Sign in to access the platform</p>
    </div>

    @if(session('status'))
    <div class="alert-success mb-6">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="input @error('email') input-error @enderror"
                   required autofocus autocomplete="username" placeholder="you@example.com">
            @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="label">Password</label>
            <input id="password" type="password" name="password"
                   class="input @error('password') input-error @enderror"
                   required autocomplete="current-password" placeholder="••••••••">
            @error('password')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-ink-300 text-ink-950 focus:ring-ink-500">
                <span class="text-sm text-ink-600">Remember me</span>
            </label>
            @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-sm text-ink-600 hover:text-ink-950 underline underline-offset-2">
                Forgot password?
            </a>
            @endif
        </div>

        <button type="submit" class="btn-primary w-full justify-center py-3 text-sm">
            Sign in
        </button>

        @if(Route::has('register'))
        <p class="text-center text-sm text-ink-500">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-ink-950 font-medium hover:underline">Request access</a>
        </p>
        @endif
    </form>
</x-guest-layout>
