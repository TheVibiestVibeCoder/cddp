<x-app-layout>
    <x-slot name="title">{{ $thread->title }}</x-slot>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-ink-400 mb-6">
        <a href="{{ route('forum.index') }}" class="hover:text-ink-950">Forum</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        <a href="{{ route('forum.category', $category) }}" class="hover:text-ink-950">{{ $category->name }}</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        <span class="text-ink-600 truncate">{{ $thread->title }}</span>
    </nav>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
        <!-- Main column -->
        <div class="xl:col-span-3 space-y-4" x-data="{ replyTo: null, replyName: '' }" @reply-to.window="replyTo = $event.detail.parentId; replyName = $event.detail.name; $nextTick(() => document.getElementById('reply-box').scrollIntoView({behavior:'smooth'}))">

            <!-- Thread header -->
            <div class="card overflow-hidden">
                <div class="bg-ink-950 px-6 py-6">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        @if($thread->is_pinned)
                        <span class="badge bg-amber-400/20 text-amber-200 text-xs">Pinned</span>
                        @endif
                        @if($thread->is_locked)
                        <span class="badge bg-red-400/20 text-red-200 text-xs">Locked</span>
                        @endif
                        @foreach($thread->tags as $tag)
                        <span class="badge bg-white/10 text-white/70 text-xs">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight leading-tight">{{ $thread->title }}</h1>
                </div>

                <!-- OP post -->
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <img src="{{ $thread->user->avatar_url }}" class="w-10 h-10 rounded-full border border-ink-200 flex-shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline gap-2 flex-wrap mb-3">
                                <span class="text-sm font-semibold text-ink-950">{{ $thread->user->name }}</span>
                                @if($thread->user->organization)
                                <span class="text-xs text-ink-400">{{ $thread->user->organization }}</span>
                                @endif
                                <span class="text-xs text-ink-400">&middot; {{ $thread->created_at->format('d M Y, H:i') }}</span>
                                <span class="badge-default text-[10px] capitalize">{{ $thread->user->role }}</span>
                            </div>
                            <div class="article-body">
                                {!! nl2br(e($thread->body)) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-3 bg-ink-50 border-t border-ink-100 flex items-center justify-between text-xs text-ink-400">
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ number_format($thread->views_count) }} views
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                            {{ $thread->replies_count }} replies
                        </span>
                    </div>
                    @if(auth()->user()->isAdmin() || $thread->user_id === auth()->id())
                    <form method="POST" action="{{ route('forum.thread.destroy', $thread) }}"
                          onsubmit="return confirm('Delete this thread? All replies will be lost.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 transition-colors">Delete thread</button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Replies -->
            @if($posts->isNotEmpty())
            <div id="replies" class="space-y-3">
                @foreach($posts as $post)
                <div class="card overflow-hidden" id="post-{{ $post->id }}">
                    <div class="p-5 flex items-start gap-4">
                        <img src="{{ $post->user->avatar_url }}" class="w-9 h-9 rounded-full border border-ink-200 flex-shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline gap-2 flex-wrap mb-3">
                                <span class="text-sm font-semibold text-ink-950">{{ $post->user->name }}</span>
                                <span class="text-xs text-ink-400">{{ $post->created_at->format('d M Y, H:i') }}</span>
                                @if($post->is_solution)
                                <span class="badge bg-emerald-100 text-emerald-700 text-xs">Solution</span>
                                @endif
                            </div>
                            <div class="article-body">
                                {!! nl2br(e($post->body)) !!}
                            </div>

                            <!-- Replies to this post -->
                            @if($post->replies->isNotEmpty())
                            <div class="mt-4 space-y-3 pl-4 border-l-2 border-ink-100">
                                @foreach($post->replies as $reply)
                                <div class="flex items-start gap-3">
                                    <img src="{{ $reply->user->avatar_url }}" class="w-7 h-7 rounded-full flex-shrink-0" alt="">
                                    <div class="flex-1">
                                        <div class="flex items-baseline gap-2 mb-1">
                                            <span class="text-xs font-semibold text-ink-950">{{ $reply->user->name }}</span>
                                            <span class="text-[11px] text-ink-400">{{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="text-sm text-ink-700 leading-relaxed">{{ $reply->body }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="px-5 py-2.5 bg-ink-50 border-t border-ink-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if(auth()->user()->canPost() && !$thread->is_locked)
                            <button type="button"
                                    class="text-xs text-ink-500 hover:text-ink-950 transition-colors"
                                    @click="replyTo = {{ $post->id }}; replyName = '{{ $post->user->name }}'; $nextTick(() => document.getElementById('reply-box').scrollIntoView({behavior:'smooth'}))">
                                Reply
                            </button>
                            @endif
                        </div>
                        @if(auth()->user()->isAdmin() || $post->user_id === auth()->id())
                        <form method="POST" action="{{ route('forum.post.destroy', $post) }}"
                              onsubmit="return confirm('Delete this reply?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 transition-colors">Delete</button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @if($posts->hasPages())
            <div class="flex justify-center">{{ $posts->links() }}</div>
            @endif
            @endif

            <!-- Reply box -->
            @if(auth()->user()->canPost() && !$thread->is_locked)
            <div class="card" id="reply-box">
                <div class="px-5 py-4 border-b border-ink-100">
                    <h3 class="text-sm font-semibold text-ink-950">Post a reply</h3>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('forum.post.store', [$category, $thread]) }}">
                        @csrf
                        <input type="hidden" name="parent_id" :value="replyTo">

                        <!-- Reply context badge -->
                        <div x-show="replyTo" x-cloak class="flex items-center gap-2 mb-3 p-2.5 bg-ink-50 rounded-lg text-sm text-ink-600 border border-ink-200">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                            Replying to <strong x-text="replyName"></strong>
                            <button type="button" class="ml-auto" @click="replyTo = null; replyName = ''">
                                <svg class="w-3.5 h-3.5 text-ink-400 hover:text-ink-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div class="flex items-start gap-3">
                            <img src="{{ auth()->user()->avatar_url }}" class="w-9 h-9 rounded-full flex-shrink-0 mt-0.5" alt="">
                            <div class="flex-1">
                                <textarea name="body" rows="5" class="input resize-y" placeholder="Write your reply…" required></textarea>
                                @error('body')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                <div class="mt-3 flex items-center justify-between">
                                    <p class="text-xs text-ink-400">Use @username to mention members</p>
                                    <button type="submit" class="btn-primary">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                                        Post reply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @elseif($thread->is_locked)
            <div class="card p-5 text-center text-sm text-ink-500 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                This thread is locked. No new replies can be posted.
            </div>
            @endif
        </div>

        <!-- Thread sidebar -->
        <div class="space-y-4">
            <div class="card p-5">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-500 mb-4">Thread Info</h3>
                <dl class="space-y-2.5 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-500">Category</dt>
                        <dd><a href="{{ route('forum.category', $category) }}" class="font-medium text-ink-950 hover:underline">{{ $category->name }}</a></dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-500">Started by</dt>
                        <dd class="font-medium text-ink-950">{{ $thread->user->name }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-500">Posted</dt>
                        <dd class="text-ink-700">{{ $thread->created_at->format('d M Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-500">Replies</dt>
                        <dd class="font-medium text-ink-950">{{ $thread->replies_count }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-500">Views</dt>
                        <dd class="font-medium text-ink-950">{{ number_format($thread->views_count) }}</dd>
                    </div>
                </dl>
            </div>

            @if($thread->tags->isNotEmpty())
            <div class="card p-5">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-500 mb-3">Tags</h3>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($thread->tags as $tag)
                    <span class="badge-default">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <a href="{{ route('forum.category', $category) }}" class="btn-secondary w-full justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                Back to {{ $category->name }}
            </a>
        </div>
    </div>
</x-app-layout>
