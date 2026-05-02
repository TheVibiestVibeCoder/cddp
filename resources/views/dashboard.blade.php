<x-app-layout>
    <x-slot name="title">Overview</x-slot>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-ink-950 tracking-tight">
                Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ explode(' ', auth()->user()->name)[0] }}
            </h1>
            <p class="text-sm text-ink-500 mt-0.5">Here's what's happening on the platform</p>
        </div>
        @if(auth()->user()->canPost())
        <a href="{{ route('data-room.create') }}" class="btn-primary btn-sm self-start">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Upload Artifact
        </a>
        @endif
    </div>

    <!-- Stats row -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <div class="card p-5">
            <p class="section-label mb-1">Documents</p>
            <p class="text-3xl font-bold text-ink-950">{{ number_format($stats['artifacts']) }}</p>
            <a href="{{ route('data-room.index') }}" class="text-xs text-ink-500 hover:text-ink-950 mt-3 flex items-center gap-1 group">
                Browse library
                <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
        <div class="card p-5">
            <p class="section-label mb-1">Discussions</p>
            <p class="text-3xl font-bold text-ink-950">{{ number_format($stats['threads']) }}</p>
            <a href="{{ route('forum.index') }}" class="text-xs text-ink-500 hover:text-ink-950 mt-3 flex items-center gap-1 group">
                View forum
                <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
        <div class="card p-5 col-span-2 lg:col-span-1">
            <p class="section-label mb-1">Members</p>
            <p class="text-3xl font-bold text-ink-950">{{ number_format($stats['users']) }}</p>
            <p class="text-xs text-ink-500 mt-3">Registered researchers</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <!-- Left: Artifacts -->
        <div class="xl:col-span-3 space-y-6">
            @if($featuredArtifacts->isNotEmpty())
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-ink-950">Featured</h2>
                    <a href="{{ route('data-room.index') }}" class="text-xs text-ink-500 hover:text-ink-950">View all →</a>
                </div>
                <div class="space-y-2">
                    @foreach($featuredArtifacts as $artifact)
                    <a href="{{ route('data-room.show', $artifact) }}" class="card-hover flex items-start gap-4 p-4 group">
                        <div class="w-10 h-10 rounded-lg bg-ink-950 flex items-center justify-center flex-shrink-0 text-lg leading-none">{{ $artifact->type_icon }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                <span class="badge-dark text-[10px]">{{ $artifact->type_label }}</span>
                                @if($artifact->category)<span class="badge-outline text-[10px]">{{ $artifact->category->name }}</span>@endif
                            </div>
                            <h3 class="text-sm font-semibold text-ink-950 group-hover:underline truncate">{{ $artifact->title }}</h3>
                            @if($artifact->summary)<p class="text-xs text-ink-500 mt-0.5 line-clamp-1">{{ $artifact->summary }}</p>@endif
                            <p class="text-[11px] text-ink-400 mt-1.5">{{ $artifact->user->name }} &middot; {{ $artifact->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-ink-950">Recently Added</h2>
                    <a href="{{ route('data-room.index') }}" class="text-xs text-ink-500 hover:text-ink-950">View all →</a>
                </div>
                @if($recentArtifacts->isEmpty())
                <div class="card p-8 text-center">
                    <p class="text-sm text-ink-400">No artifacts yet.</p>
                    @if(auth()->user()->canPost())
                    <a href="{{ route('data-room.create') }}" class="btn-primary btn-sm mt-4 inline-flex">Upload the first one</a>
                    @endif
                </div>
                @else
                <div class="card divide-y divide-ink-100">
                    @foreach($recentArtifacts as $artifact)
                    <a href="{{ route('data-room.show', $artifact) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-ink-50 transition-colors group">
                        <span class="text-lg flex-shrink-0">{{ $artifact->type_icon }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-ink-950 group-hover:underline truncate">{{ $artifact->title }}</p>
                            <p class="text-[11px] text-ink-400">{{ $artifact->user->name }} &middot; {{ $artifact->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="badge-default text-[10px] flex-shrink-0">{{ $artifact->type_label }}</span>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Right: Forum -->
        <div class="xl:col-span-2">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-ink-950">Forum Activity</h2>
                <a href="{{ route('forum.index') }}" class="text-xs text-ink-500 hover:text-ink-950">View all →</a>
            </div>
            @if($recentThreads->isEmpty())
            <div class="card p-8 text-center">
                <p class="text-sm text-ink-400">No threads yet.</p>
            </div>
            @else
            <div class="card divide-y divide-ink-100">
                @foreach($recentThreads as $thread)
                <a href="{{ route('forum.thread', [$thread->category, $thread]) }}" class="block px-4 py-3.5 hover:bg-ink-50 transition-colors group">
                    <p class="text-sm font-medium text-ink-950 group-hover:underline line-clamp-2">{{ $thread->title }}</p>
                    <div class="flex items-center gap-2 mt-1.5">
                        <span class="badge-default text-[10px]">{{ $thread->category->name }}</span>
                        <span class="text-[11px] text-ink-400">{{ $thread->replies_count }} replies &middot; {{ $thread->created_at->diffForHumans() }}</span>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
