<x-app-layout>
    <x-slot name="title">{{ $artifact->title }}</x-slot>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-ink-400 mb-6">
        <a href="{{ route('data-room.index') }}" class="hover:text-ink-950">Data Room</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        @if($artifact->category)
        <a href="{{ route('data-room.index', ['category' => $artifact->category_id]) }}" class="hover:text-ink-950">{{ $artifact->category->name }}</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        @endif
        <span class="text-ink-600 truncate">{{ $artifact->title }}</span>
    </nav>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Main content -->
        <div class="xl:col-span-2 space-y-6">

            <!-- Artifact header -->
            <div class="card overflow-hidden">
                <div class="bg-ink-950 px-6 py-8">
                    <div class="flex items-start gap-4">
                        <div class="text-5xl leading-none flex-shrink-0">{{ $artifact->type_icon }}</div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <span class="badge bg-white/15 text-white text-xs">{{ $artifact->type_label }}</span>
                                @if($artifact->is_featured)
                                <span class="badge bg-amber-400/20 text-amber-200 text-xs">Featured</span>
                                @endif
                                @if($artifact->category)
                                <span class="badge bg-white/10 text-white/70 text-xs">{{ $artifact->category->name }}</span>
                                @endif
                            </div>
                            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight leading-tight">{{ $artifact->title }}</h1>
                            @if($artifact->summary)
                            <p class="text-white/60 text-sm mt-2 leading-relaxed">{{ $artifact->summary }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Meta bar -->
                <div class="px-6 py-4 border-b border-ink-100 flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-ink-500">
                    <div class="flex items-center gap-2">
                        <img src="{{ $artifact->user->avatar_url }}" class="w-5 h-5 rounded-full" alt="">
                        <span>{{ $artifact->user->name }}</span>
                    </div>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        {{ $artifact->created_at->format('d M Y') }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        {{ number_format($artifact->views_count) }} views
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        {{ number_format($artifact->downloads_count) }} downloads
                    </span>
                    @if($artifact->language && $artifact->language !== 'en')
                    <span class="uppercase">{{ $artifact->language }}</span>
                    @endif
                </div>

                <!-- Tags -->
                @if($artifact->tags->isNotEmpty())
                <div class="px-6 py-3 border-b border-ink-100 flex flex-wrap gap-1.5">
                    @foreach($artifact->tags as $tag)
                    <a href="{{ route('data-room.index', ['tag' => $tag->slug]) }}" class="badge-outline hover:bg-ink-100 transition-colors">{{ $tag->name }}</a>
                    @endforeach
                </div>
                @endif

                <!-- Description -->
                @if($artifact->description)
                <div class="px-6 py-5 article-body">
                    {!! nl2br(e($artifact->description)) !!}
                </div>
                @endif

                <!-- Actions -->
                <div class="px-6 py-4 bg-ink-50 border-t border-ink-100 flex flex-wrap items-center gap-3">
                    @if($artifact->file_path)
                    <a href="{{ route('data-room.download', $artifact) }}" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Download
                        @if($artifact->file_size)
                        <span class="text-white/60 text-xs">{{ $artifact->file_size }}</span>
                        @endif
                    </a>
                    @elseif($artifact->external_url)
                    <a href="{{ $artifact->external_url }}" target="_blank" rel="noopener noreferrer" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        Open link
                    </a>
                    @endif

                    @if(auth()->user()->isAdmin() || $artifact->user_id === auth()->id())
                    <div class="ml-auto flex items-center gap-2">
                        <a href="{{ route('data-room.edit', $artifact) }}" class="btn-secondary btn-sm">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Edit
                        </a>
                        <form method="POST" action="{{ route('data-room.destroy', $artifact) }}"
                              onsubmit="return confirm('Delete this artifact? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-ghost btn-sm text-red-600 hover:bg-red-50 hover:text-red-700">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Delete
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Comments -->
            <div id="comments" class="card">
                <div class="px-5 py-4 border-b border-ink-100">
                    <h2 class="text-sm font-semibold text-ink-950">Comments <span class="text-ink-400 font-normal">({{ $artifact->comments->count() }})</span></h2>
                </div>

                @if($artifact->comments->isEmpty())
                <div class="px-5 py-8 text-center text-sm text-ink-400">No comments yet. Be the first to comment.</div>
                @else
                <div class="divide-y divide-ink-100">
                    @foreach($artifact->comments as $comment)
                    @include('partials.comment', ['comment' => $comment])
                    @endforeach
                </div>
                @endif

                @if(auth()->user()->canPost())
                <div class="px-5 py-4 border-t border-ink-100 bg-ink-50">
                    <form method="POST" action="{{ route('data-room.comments.store', $artifact) }}">
                        @csrf
                        <div class="flex items-start gap-3">
                            <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full flex-shrink-0 mt-1" alt="">
                            <div class="flex-1">
                                <textarea name="body" rows="3" class="input resize-none" placeholder="Add a comment…" required></textarea>
                                @error('body')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                <div class="mt-2 flex justify-end">
                                    <button type="submit" class="btn-primary btn-sm">Post comment</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @else
                <div class="px-5 py-3 border-t border-ink-100 text-xs text-ink-500 text-center">You need posting permissions to comment.</div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-5">
            <!-- File info -->
            @if($artifact->file_name || $artifact->source || $artifact->published_date)
            <div class="card p-5">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-500 mb-4">Details</h3>
                <dl class="space-y-3 text-sm">
                    @if($artifact->file_name)
                    <div>
                        <dt class="text-xs text-ink-400 mb-0.5">File name</dt>
                        <dd class="text-ink-800 font-mono text-xs truncate">{{ $artifact->file_name }}</dd>
                    </div>
                    @endif
                    @if($artifact->file_size)
                    <div>
                        <dt class="text-xs text-ink-400 mb-0.5">File size</dt>
                        <dd class="text-ink-800">{{ $artifact->file_size }}</dd>
                    </div>
                    @endif
                    @if($artifact->source)
                    <div>
                        <dt class="text-xs text-ink-400 mb-0.5">Source</dt>
                        <dd class="text-ink-800">{{ $artifact->source }}</dd>
                    </div>
                    @endif
                    @if($artifact->published_date)
                    <div>
                        <dt class="text-xs text-ink-400 mb-0.5">Published date</dt>
                        <dd class="text-ink-800">{{ $artifact->published_date->format('d M Y') }}</dd>
                    </div>
                    @endif
                    @if($artifact->language)
                    <div>
                        <dt class="text-xs text-ink-400 mb-0.5">Language</dt>
                        <dd class="text-ink-800 uppercase">{{ $artifact->language }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

            <!-- Uploader -->
            <div class="card p-5">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-500 mb-4">Uploaded by</h3>
                <div class="flex items-center gap-3">
                    <img src="{{ $artifact->user->avatar_url }}" class="w-10 h-10 rounded-full border border-ink-200" alt="">
                    <div>
                        <p class="text-sm font-medium text-ink-950">{{ $artifact->user->name }}</p>
                        @if($artifact->user->organization)
                        <p class="text-xs text-ink-500">{{ $artifact->user->organization }}</p>
                        @endif
                        <p class="text-xs text-ink-400">{{ $artifact->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Related -->
            @if($related->isNotEmpty())
            <div class="card p-5">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-500 mb-4">Related</h3>
                <div class="space-y-3">
                    @foreach($related as $item)
                    <a href="{{ route('data-room.show', $item) }}" class="flex items-start gap-3 group">
                        <span class="text-xl flex-shrink-0 mt-0.5">{{ $item->type_icon }}</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-ink-950 group-hover:underline line-clamp-2">{{ $item->title }}</p>
                            <p class="text-xs text-ink-400 mt-0.5">{{ $item->created_at->format('d M Y') }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
