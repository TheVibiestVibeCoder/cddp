<x-app-layout>
    <x-slot name="title">Admin Dashboard</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-ink-950 tracking-tight">Admin Dashboard</h1>
        <p class="text-sm text-ink-500 mt-0.5">Platform overview and management</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="card p-5">
            <p class="section-label mb-1">Total users</p>
            <p class="text-3xl font-bold text-ink-950">{{ $stats['users'] }}</p>
            <a href="{{ route('admin.users') }}" class="text-xs text-ink-500 hover:text-ink-950 mt-2 flex items-center gap-1 group">
                Manage <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
        <div class="card p-5">
            <p class="section-label mb-1">Artifacts</p>
            <p class="text-3xl font-bold text-ink-950">{{ $stats['artifacts'] }}</p>
            <a href="{{ route('data-room.index') }}" class="text-xs text-ink-500 hover:text-ink-950 mt-2 flex items-center gap-1 group">
                Browse <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
        <div class="card p-5">
            <p class="section-label mb-1">Forum threads</p>
            <p class="text-3xl font-bold text-ink-950">{{ $stats['threads'] }}</p>
            <a href="{{ route('forum.index') }}" class="text-xs text-ink-500 hover:text-ink-950 mt-2 flex items-center gap-1 group">
                View <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
        <div class="card p-5">
            <p class="section-label mb-1">Admins</p>
            <p class="text-3xl font-bold text-ink-950">{{ $stats['admins'] }}</p>
            <p class="text-xs text-ink-500 mt-2">Platform administrators</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-ink-950">Recent Users</h2>
                <a href="{{ route('admin.users') }}" class="text-xs text-ink-500 hover:text-ink-950">View all →</a>
            </div>
            <div class="card">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentUsers as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $user->avatar_url }}" class="w-7 h-7 rounded-full" alt="">
                                    <div>
                                        <p class="font-medium text-ink-950 text-sm">{{ $user->name }}</p>
                                        <p class="text-xs text-ink-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $user->role === 'admin' ? 'badge-dark' : 'badge-default' }} capitalize">{{ $user->role }}</span>
                            </td>
                            <td class="text-ink-500">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Artifacts -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-ink-950">Recent Uploads</h2>
                <a href="{{ route('data-room.index') }}" class="text-xs text-ink-500 hover:text-ink-950">View all →</a>
            </div>
            <div class="card">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th>Artifact</th>
                            <th>Type</th>
                            <th>Uploaded</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentArtifacts as $artifact)
                        <tr>
                            <td>
                                <a href="{{ route('data-room.show', $artifact) }}" class="font-medium text-ink-950 hover:underline text-sm line-clamp-1">
                                    {{ $artifact->title }}
                                </a>
                                <p class="text-xs text-ink-400">{{ $artifact->user->name }}</p>
                            </td>
                            <td><span class="badge-default capitalize">{{ $artifact->type }}</span></td>
                            <td class="text-ink-500">{{ $artifact->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick links -->
    <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('admin.users') }}" class="card p-5 hover:border-ink-400 transition-colors group flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-ink-950 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-ink-950 group-hover:underline">Manage Users</p>
                <p class="text-xs text-ink-500">Roles & permissions</p>
            </div>
        </a>
        <a href="{{ route('admin.categories') }}" class="card p-5 hover:border-ink-400 transition-colors group flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-ink-950 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-ink-950 group-hover:underline">Categories</p>
                <p class="text-xs text-ink-500">Data room structure</p>
            </div>
        </a>
        <a href="{{ route('admin.tags') }}" class="card p-5 hover:border-ink-400 transition-colors group flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-ink-950 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 10V5a2 2 0 012-2z" /></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-ink-950 group-hover:underline">Tags</p>
                <p class="text-xs text-ink-500">Taxonomy management</p>
            </div>
        </a>
    </div>
</x-app-layout>
