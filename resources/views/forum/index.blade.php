<x-app-layout>
    <x-slot name="title">Forum</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-ink-950 tracking-tight">Forum</h1>
            <p class="text-sm text-ink-500 mt-0.5">Community discussions and exchange</p>
        </div>
    </div>

    <!-- Categories -->
    <div class="space-y-3 mb-8">
        @forelse($categories as $category)
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

                <!-- Arrow -->
                <a href="{{ route('forum.category', $category) }}" class="flex-shrink-0 self-center p-2 rounded-lg hover:bg-ink-100 transition-colors text-ink-400 hover:text-ink-950">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>
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
</x-app-layout>
