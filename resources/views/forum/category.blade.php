<x-app-layout>
    <x-slot name="title">{{ $category->name }}</x-slot>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-ink-400 mb-6">
        <a href="{{ route('forum.index') }}" class="hover:text-ink-950">Forum</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        <span class="text-ink-600">{{ $category->name }}</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            @if($category->image_url)
            <img src="{{ $category->image_url }}" class="w-12 h-12 rounded-xl object-cover flex-shrink-0 border border-ink-200" alt="">
            @else
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
                 style="background-color: {{ $category->color }}">
                {{ $category->icon ?: mb_substr($category->name, 0, 1) }}
            </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-ink-950 tracking-tight">{{ $category->name }}</h1>
                @if($category->description)
                <p class="text-sm text-ink-500 mt-0.5">{{ $category->description }}</p>
                @endif
            </div>
        </div>
        @if(auth()->user()->canPost())
        <a href="{{ route('forum.thread.create', $category) }}" class="btn-primary btn-sm self-start">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            New Thread
        </a>
        @endif
    </div>

    <!-- Threads -->
    @if($threads->isEmpty())
    <div class="card p-12 text-center">
        <svg class="w-12 h-12 text-ink-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
        <h3 class="text-base font-semibold text-ink-950 mb-1">No threads yet</h3>
        <p class="text-sm text-ink-500">Be the first to start a discussion in this category.</p>
        @if(auth()->user()->canPost())
        <a href="{{ route('forum.thread.create', $category) }}" class="btn-primary btn-sm mt-5 inline-flex">Start a thread</a>
        @endif
    </div>
    @else
    <div class="card divide-y divide-ink-100">
        @foreach($threads as $thread)
        <div class="flex items-start gap-4 px-4 py-4 hover:bg-ink-50 transition-colors {{ $thread->is_pinned ? 'bg-ink-50/50' : '' }}">
            <!-- Status indicator -->
            <div class="flex-shrink-0 mt-0.5">
                @if($thread->is_locked)
                <div class="w-8 h-8 rounded-full bg-ink-100 flex items-center justify-center" title="Locked">
                    <svg class="w-4 h-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>
                @elseif($thread->is_pinned)
                <div class="w-8 h-8 rounded-full bg-ink-950 flex items-center justify-center" title="Pinned">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                </div>
                @else
                <div class="w-8 h-8 rounded-full bg-ink-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                </div>
                @endif
            </div>

            <!-- Thread info -->
            <div class="flex-1 min-w-0">
                <a href="{{ route('forum.thread', [$category, $thread]) }}"
                   class="text-sm font-semibold text-ink-950 hover:underline line-clamp-2">
                    {{ $thread->title }}
                </a>

                @if($thread->tags->isNotEmpty())
                <div class="flex flex-wrap gap-1 mt-1.5">
                    @foreach($thread->tags->take(3) as $tag)
                    <span class="badge-default text-[10px]">{{ $tag->name }}</span>
                    @endforeach
                </div>
                @endif

                <div class="flex items-center gap-2 mt-1.5 text-[11px] text-ink-400">
                    <img src="{{ $thread->user->avatar_url }}" class="w-4 h-4 rounded-full object-cover" alt="">
                    <span>{{ $thread->user->name }}</span>
                    <span>&middot;</span>
                    <span>{{ $thread->created_at->diffForHumans() }}</span>
                </div>
            </div>

            <!-- Stats + last reply -->
            <div class="flex-shrink-0 text-right hidden sm:block">
                <div class="flex items-center gap-3 text-xs text-ink-500 mb-2">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                        {{ $thread->replies_count }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $thread->views_count }}
                    </span>
                </div>
                @if($thread->last_reply_at)
                <div class="text-[11px] text-ink-400">
                    <div>{{ $thread->last_reply_at->diffForHumans() }}</div>
                    @if($thread->lastReplyUser)
                    <div>by {{ $thread->lastReplyUser->name }}</div>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($threads->hasPages())
    <div class="mt-6 flex justify-center">{{ $threads->links() }}</div>
    @endif
    @endif
</x-app-layout>
