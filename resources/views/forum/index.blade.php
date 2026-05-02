<x-app-layout>
    <x-slot name="title">Forum</x-slot>

    <div x-data="{ createForum: false }" @keydown.escape.window="createForum = false">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-ink-950 tracking-tight">Forum</h1>
            <p class="text-sm text-ink-500 mt-0.5">Community discussions and exchange</p>
        </div>
        @if(auth()->user()->isAdmin())
        <button @click="createForum = true" class="btn-primary flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            New Forum
        </button>
        @endif
    </div>

    <!-- Categories -->
    <div class="space-y-3 mb-8">
        @forelse($categories as $category)
        <div x-data="{ editOpen: false }">
        <div class="card hover:border-ink-300 transition-colors">
            <div class="p-5 flex items-start gap-4">
                <!-- Icon -->
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 text-white text-lg font-bold"
                     style="background-color: {{ $category->color }}">
                    {{ $category->icon ?: mb_substr($category->name, 0, 1) }}
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-baseline justify-between gap-4 flex-wrap">
                        <a href="{{ route('forum.category', $category) }}" class="text-base font-semibold text-ink-950 hover:underline">
                            {{ $category->name }}
                        </a>
                        <div class="flex items-center gap-4 text-xs text-ink-400 flex-shrink-0">
                            <span>{{ number_format($category->threads_count) }} thread{{ $category->threads_count !== 1 ? 's' : '' }}</span>
                            <span>{{ number_format($category->posts_count) }} post{{ $category->posts_count !== 1 ? 's' : '' }}</span>
                        </div>
                    </div>
                    @if($category->description)
                    <p class="text-sm text-ink-500 mt-1">{{ $category->description }}</p>
                    @endif

                    @if($category->latestThread)
                    <div class="mt-3 flex items-center gap-2 text-xs text-ink-400">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Latest:</span>
                        <a href="{{ route('forum.thread', [$category, $category->latestThread]) }}"
                           class="text-ink-700 hover:text-ink-950 hover:underline truncate max-w-xs">
                            {{ $category->latestThread->title }}
                        </a>
                        <span class="flex-shrink-0">by {{ $category->latestThread->user?->name }}</span>
                        <span class="flex-shrink-0">&middot; {{ $category->latestThread->last_reply_at?->diffForHumans() }}</span>
                    </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-1 flex-shrink-0 self-center">
                    @if(auth()->user()->isAdmin())
                    <button @click="editOpen = true"
                            class="p-2 rounded-lg hover:bg-ink-100 transition-colors text-ink-400 hover:text-ink-950"
                            title="Edit forum">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                    @endif
                    <a href="{{ route('forum.category', $category) }}" class="p-2 rounded-lg hover:bg-ink-100 transition-colors text-ink-400 hover:text-ink-950">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Edit modal for this category -->
        @if(auth()->user()->isAdmin())
        <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="editOpen = false">
            <div class="absolute inset-0 bg-black/50" @click="editOpen = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="px-6 py-5 border-b border-ink-100 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-ink-950">Edit Forum</h2>
                    <button @click="editOpen = false" class="p-1 rounded-lg hover:bg-ink-100 text-ink-400 hover:text-ink-950 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('forum.category.update', $category) }}" class="p-6 space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="label">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" class="input" required maxlength="100"
                               value="{{ $category->name }}">
                    </div>
                    <div>
                        <label class="label">Description</label>
                        <input type="text" name="description" class="input" maxlength="500"
                               value="{{ $category->description }}">
                    </div>
                    <div x-data="{ icon: '{{ addslashes($category->icon ?? '💬') }}' }">
                        <label class="label">Icon</label>
                        <div class="flex flex-wrap gap-2 mb-2">
                            @foreach(['💬','📢','🔬','📰','🌐','🗂️','📊','🤝','⚠️','🔍','📝','💡'] as $emoji)
                            <button type="button"
                                    @click="icon = '{{ $emoji }}'; $refs.editIconInput{{ $category->id }}.value = '{{ $emoji }}'"
                                    class="w-9 h-9 rounded-lg border border-ink-200 hover:border-ink-950 text-lg transition-colors"
                                    :class="icon === '{{ $emoji }}' ? 'border-ink-950 bg-ink-50' : ''">{{ $emoji }}</button>
                            @endforeach
                        </div>
                        <input type="hidden" name="icon" x-ref="editIconInput{{ $category->id }}" :value="icon">
                    </div>
                    <div x-data="{ color: '{{ $category->color ?? '#18181b' }}' }">
                        <label class="label">Color</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['#18181b','#1d4ed8','#15803d','#b91c1c','#7c3aed','#0891b2','#c2410c','#be185d'] as $c)
                            <button type="button"
                                    @click="color = '{{ $c }}'; $refs.editColorInput{{ $category->id }}.value = '{{ $c }}'"
                                    class="w-8 h-8 rounded-lg border-2 transition-all"
                                    :class="color === '{{ $c }}' ? 'border-ink-950 scale-110' : 'border-transparent'"
                                    style="background-color: {{ $c }}"></button>
                            @endforeach
                        </div>
                        <input type="hidden" name="color" x-ref="editColorInput{{ $category->id }}" :value="color">
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="editOpen = false" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
        </div>{{-- close x-data per category --}}
        @empty
        <div class="card p-12 text-center">
            <svg class="w-12 h-12 text-ink-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
            <p class="text-sm text-ink-500">No forum categories yet. Admins can add them in the admin panel.</p>
        </div>
        @endforelse
    </div>

    <!-- Recent threads across all categories -->
    @if($recentThreads->isNotEmpty())
    <div>
        <h2 class="text-sm font-semibold text-ink-950 mb-3">Recent Discussions</h2>
        <div class="card divide-y divide-ink-100">
            @foreach($recentThreads as $thread)
            <div class="px-4 py-4 flex items-start gap-4 hover:bg-ink-50 transition-colors">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('forum.thread', [$thread->category, $thread]) }}" class="text-sm font-medium text-ink-950 hover:underline line-clamp-1">
                        @if($thread->is_pinned)
                        <svg class="inline w-3.5 h-3.5 text-ink-500 mr-1 -mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                        @endif
                        {{ $thread->title }}
                    </a>
                    <div class="flex items-center gap-2 mt-1 text-[11px] text-ink-400">
                        <a href="{{ route('forum.category', $thread->category) }}" class="badge-default text-[10px] hover:bg-ink-200">{{ $thread->category->name }}</a>
                        <span>{{ $thread->user->name }}</span>
                        <span>&middot;</span>
                        <span>{{ $thread->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-xs text-ink-400 flex-shrink-0">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                        {{ $thread->replies_count }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $thread->views_count }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Create Forum Modal -->
    @if(auth()->user()->canPost())
    <div x-show="createForum" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="createForum = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="px-6 py-5 border-b border-ink-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-ink-950">Create New Forum</h2>
                <button @click="createForum = false" class="p-1 rounded-lg hover:bg-ink-100 text-ink-400 hover:text-ink-950 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('forum.category.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="label">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="input @error('name') input-error @enderror"
                           placeholder="e.g. Research Methods" required maxlength="100"
                           value="{{ old('name') }}">
                    @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">Description</label>
                    <input type="text" name="description" class="input"
                           placeholder="What is this forum about?" maxlength="500"
                           value="{{ old('description') }}">
                </div>
                <div x-data="{ icon: '{{ old('icon', '💬') }}' }">
                    <label class="label">Icon</label>
                    <div class="flex flex-wrap gap-2 mb-2">
                        @foreach(['💬','📢','🔬','📰','🌐','🗂️','📊','🤝','⚠️','🔍','📝','💡'] as $emoji)
                        <button type="button"
                                @click="icon = '{{ $emoji }}'; $refs.iconInput.value = '{{ $emoji }}'"
                                class="w-9 h-9 rounded-lg border border-ink-200 hover:border-ink-950 text-lg transition-colors"
                                :class="icon === '{{ $emoji }}' ? 'border-ink-950 bg-ink-50' : ''">{{ $emoji }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="icon" x-ref="iconInput" :value="icon">
                </div>
                <div x-data="{ color: '{{ old('color', '#18181b') }}' }">
                    <label class="label">Color</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['#18181b','#1d4ed8','#15803d','#b91c1c','#7c3aed','#0891b2','#c2410c','#be185d'] as $c)
                        <button type="button"
                                @click="color = '{{ $c }}'; $refs.colorInput.value = '{{ $c }}'"
                                class="w-8 h-8 rounded-lg border-2 transition-all"
                                :class="color === '{{ $c }}' ? 'border-ink-950 scale-110' : 'border-transparent'"
                                style="background-color: {{ $c }}"></button>
                        @endforeach
                    </div>
                    <input type="hidden" name="color" x-ref="colorInput" :value="color">
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="createForum = false" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Create Forum</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    </div>{{-- close x-data --}}
</x-app-layout>
