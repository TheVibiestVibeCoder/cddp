<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CDDP') }} — {{ $title ?? 'Platform' }}</title>
    <meta name="description" content="{{ $description ?? 'Centralised Disinformation Data Platform' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-ink-50" x-data="{ sidebarOpen: false }">

    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-ink-200 flex flex-col transform transition-transform duration-200 ease-out"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        <!-- Logo -->
        <div class="flex items-center gap-3 px-5 py-5 border-b border-ink-100">
            <div class="w-8 h-8 bg-ink-950 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <div>
                <div class="text-sm font-bold text-ink-950 leading-tight tracking-tight">CDDP</div>
                <div class="text-[10px] font-medium text-ink-400 uppercase tracking-widest">Data Platform</div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-4">
            <div>
                <div class="section-label px-3 mb-1.5">Main</div>
                <div class="space-y-0.5">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'nav-link-active' : 'nav-link' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        Overview
                    </a>
                </div>
            </div>

            <div>
                <div class="section-label px-3 mb-1.5">Data Room</div>
                <div class="space-y-0.5">
                    <a href="{{ route('data-room.index') }}" class="{{ request()->routeIs('data-room.index') ? 'nav-link-active' : 'nav-link' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        Library
                    </a>
                    @if(auth()->user()->canPost())
                    <a href="{{ route('data-room.create') }}" class="{{ request()->routeIs('data-room.create') ? 'nav-link-active' : 'nav-link' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Upload
                    </a>
                    @endif
                </div>
            </div>

            <div>
                <div class="section-label px-3 mb-1.5">Community</div>
                <div class="space-y-0.5">
                    <a href="{{ route('forum.index') }}" class="{{ request()->routeIs('forum.*') ? 'nav-link-active' : 'nav-link' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        Forum
                    </a>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
            <div>
                <div class="section-label px-3 mb-1.5">Admin</div>
                <div class="space-y-0.5">
                    <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'nav-link-active' : 'nav-link' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'nav-link-active' : 'nav-link' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Users
                    </a>
                    <a href="{{ route('admin.categories') }}" class="{{ request()->routeIs('admin.categories*') ? 'nav-link-active' : 'nav-link' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                        Categories
                    </a>
                    <a href="{{ route('admin.tags') }}" class="{{ request()->routeIs('admin.tags*') ? 'nav-link-active' : 'nav-link' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 10V5a2 2 0 012-2z" /></svg>
                        Tags
                    </a>
                </div>
            </div>
            @endif
        </nav>

        <!-- User area -->
        <div class="border-t border-ink-100 p-3" x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-ink-50 transition-colors text-left">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0 border border-ink-200">
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-ink-950 truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-ink-400 capitalize">{{ auth()->user()->role }}</div>
                </div>
                <svg class="w-3.5 h-3.5 text-ink-400 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                 class="mt-1 space-y-0.5" @click.away="open = false">
                <a href="{{ route('profile.edit') }}" class="nav-link">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Profile Settings
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full nav-link text-red-600 hover:bg-red-50 hover:text-red-700">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main area -->
    <div class="lg:pl-64 min-h-screen flex flex-col">
        <!-- Mobile topbar -->
        <header class="lg:hidden sticky top-0 z-30 flex items-center justify-between bg-white/95 backdrop-blur-sm border-b border-ink-200 px-4 h-14">
            <button @click="sidebarOpen = true" class="p-1.5 rounded-lg hover:bg-ink-100 transition-colors -ml-1.5">
                <svg class="w-5 h-5 text-ink-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>
            <span class="text-sm font-bold text-ink-950 tracking-tight">CDDP</span>
            <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover border border-ink-200" alt="">
        </header>

        <!-- Flash messages -->
        @if(session('success') || session('error'))
        <div class="px-6 pt-4 space-y-2">
            @if(session('success'))
            <div class="alert-success animate-fade-in" x-data x-init="setTimeout(() => $el.remove(), 4000)">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="alert-error animate-fade-in" x-data x-init="setTimeout(() => $el.remove(), 5000)">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                {{ session('error') }}
            </div>
            @endif
        </div>
        @endif

        <!-- Page content -->
        <main class="flex-1 px-4 sm:px-6 py-6 max-w-screen-2xl mx-auto w-full">
            {{ $slot }}
        </main>

        <footer class="border-t border-ink-200 px-6 py-4">
            <p class="text-xs text-ink-400 text-center">Centralised Disinformation Data Platform &mdash; Internal Research Tool</p>
        </footer>
    </div>

    <style>[x-cloak] { display: none !important; }</style>
</body>
</html>
