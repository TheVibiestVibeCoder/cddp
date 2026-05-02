<x-app-layout>
    <x-slot name="title">User Management</x-slot>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-ink-400 mb-6">
        <a href="{{ route('admin.index') }}" class="hover:text-ink-950">Admin</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        <span class="text-ink-600">Users</span>
    </nav>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-ink-950 tracking-tight">Users</h1>
            <p class="text-sm text-ink-500 mt-0.5">{{ $users->total() }} registered members</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" name="search" value="{{ request('search') }}" class="input pl-9" placeholder="Search by name or email…">
        </div>
        <select name="role" class="input sm:w-40" onchange="this.form.submit()">
            <option value="">All roles</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
            <option value="readonly" {{ request('role') === 'readonly' ? 'selected' : '' }}>Read only</option>
        </select>
        <button type="submit" class="btn-primary btn-sm">Search</button>
        @if(request()->hasAny(['search', 'role']))
        <a href="{{ route('admin.users') }}" class="btn-secondary btn-sm">Clear</a>
        @endif
    </form>

    <!-- Table -->
    <div class="card overflow-hidden">
        <table class="data-table w-full">
            <thead>
                <tr>
                    <th>User</th>
                    <th class="hidden sm:table-cell">Organisation</th>
                    <th>Role</th>
                    <th class="hidden lg:table-cell">Joined</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0" alt="">
                            <div>
                                <p class="font-medium text-ink-950">{{ $user->name }}</p>
                                <p class="text-xs text-ink-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="hidden sm:table-cell text-ink-600">{{ $user->organization ?: '—' }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.users.role', $user) }}" class="inline">
                            @csrf @method('PATCH')
                            <select name="role" onchange="this.form.submit()"
                                    class="text-xs font-medium px-2 py-1 rounded-lg border border-ink-200 bg-white focus:outline-none focus:ring-1 focus:ring-ink-500 cursor-pointer
                                           {{ $user->role === 'admin' ? 'bg-ink-950 text-white border-ink-950' : '' }}">
                                <option value="readonly" {{ $user->role === 'readonly' ? 'selected' : '' }}>Read only</option>
                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </form>
                    </td>
                    <td class="hidden lg:table-cell text-ink-500">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="text-right">
                        @if($user->id !== auth()->id())
                        <div class="flex items-center justify-end gap-1">
                            <form method="POST" action="{{ route('admin.users.force-reset', $user) }}"
                                  onsubmit="return confirm('Send a password reset email to {{ addslashes($user->email) }}? This will force them to set a new password.')">
                                @csrf
                                <button type="submit" title="Force password reset"
                                        class="btn-ghost btn-sm text-amber-600 hover:bg-amber-50 hover:text-amber-700">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This is permanent.')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Delete user"
                                        class="btn-ghost btn-sm text-red-600 hover:bg-red-50 hover:text-red-700">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                        @else
                        <span class="text-xs text-ink-300 px-2">You</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-ink-400 py-12">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="mt-6 flex justify-center">{{ $users->links() }}</div>
    @endif
</x-app-layout>
