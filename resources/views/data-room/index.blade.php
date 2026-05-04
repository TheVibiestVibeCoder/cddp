<x-app-layout>
    <x-slot name="title">Data Room</x-slot>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-ink-950 tracking-tight">Data Room</h1>
            <p class="text-sm text-ink-500 mt-0.5">{{ $artifacts->total() }} artifact{{ $artifacts->total() !== 1 ? 's' : '' }} available</p>
        </div>
        @if(auth()->user()->canPost())
        <a href="{{ route('data-room.create') }}" class="btn-primary btn-sm self-start">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Upload Artifact
        </a>
        @endif
    </div>

    @php
        $browsing = !request()->hasAny(['search', 'type', 'category', 'tag', 'sort']) && $categories->isNotEmpty();
    @endphp

    @if($browsing)

    {{-- ── Category browse ──────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-ink-500">Browse by category</p>
        <a href="{{ route('data-room.index', ['sort' => 'latest']) }}"
           class="text-sm text-ink-500 hover:text-ink-950 transition-colors flex items-center gap-1">
            View all artifacts
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($categories as $cat)
        <a href="{{ route('data-room.index', ['category' => $cat->id]) }}"
           class="card-hover group p-5 flex flex-col gap-3">
            <div class="flex items-start justify-between gap-3">
                @if($cat->icon)
                <span class="text-2xl leading-none">{{ $cat->icon }}</span>
                @else
                <div class="w-9 h-9 rounded-lg bg-ink-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                    </svg>
                </div>
                @endif
                <span class="text-xs text-ink-400 font-medium tabular-nums mt-0.5 flex-shrink-0">
                    {{ $cat->artifacts_count }} artifact{{ $cat->artifacts_count !== 1 ? 's' : '' }}
                </span>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-ink-950 group-hover:underline underline-offset-2">{{ $cat->name }}</h3>
                @if($cat->description)
                <p class="text-xs text-ink-500 mt-1 line-clamp-2">{{ $cat->description }}</p>
                @endif
            </div>
            @if($cat->children->isNotEmpty())
            <div class="flex flex-wrap gap-1">
                @foreach($cat->children->take(4) as $child)
                <span class="badge-outline text-[10px]">{{ $child->name }}</span>
                @endforeach
                @if($cat->children->count() > 4)
                <span class="text-[10px] text-ink-400">+{{ $cat->children->count() - 4 }} more</span>
                @endif
            </div>
            @endif
        </a>
        @endforeach
    </div>

    @else

    <div class="flex flex-col lg:flex-row gap-6">

        <!-- ── Sidebar filters ─────────────────────────────────────────── -->
        <aside class="w-full lg:w-60 flex-shrink-0" x-data="{ mobileOpen: false }">

            <!-- Mobile toggle -->
            <button @click="mobileOpen = !mobileOpen"
                    class="lg:hidden w-full btn-secondary justify-between mb-4">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                    Filters
                </span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="mobileOpen ? 'rotate-180' : ''"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="mobileOpen" x-cloak class="lg:hidden mb-4"></div>

            <form method="GET" action="{{ route('data-room.index') }}" id="filter-form"
                  class="hidden lg:block space-y-3" x-data>

                <!-- ── Search (always visible) ── -->
                <div class="card p-4">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-400 pointer-events-none"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="input pl-9 pr-3 py-2"
                               placeholder="Search artifacts…"
                               x-on:input.debounce.400ms="$el.form.submit()">
                    </div>
                </div>

                <!-- ── Type ── -->
                <div class="card overflow-hidden" x-data="{ open: {{ request('type') ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-3 text-xs font-semibold uppercase tracking-wider text-ink-500 hover:text-ink-950 hover:bg-ink-50 transition-colors">
                        <span class="flex items-center gap-2">
                            Type
                            @if(request('type'))
                            <span class="badge-dark text-[10px] normal-case tracking-normal font-medium">{{ request('type') }}</span>
                            @endif
                        </span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 flex-shrink-0"
                             :class="open ? 'rotate-180' : ''"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="border-t border-ink-100 px-4 py-3 space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="type" value=""
                                   {{ !request('type') ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 text-ink-950 border-ink-300"
                                   onchange="this.form.submit()">
                            <span class="text-sm text-ink-700 group-hover:text-ink-950">All types</span>
                        </label>
                        @foreach($types as $type)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="type" value="{{ $type }}"
                                   {{ request('type') === $type ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 text-ink-950 border-ink-300"
                                   onchange="this.form.submit()">
                            <span class="text-sm text-ink-700 group-hover:text-ink-950 capitalize">{{ $type }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- ── Category ── -->
                @if($categories->isNotEmpty())
                <div class="card overflow-hidden" x-data="{ open: {{ request('category') ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-3 text-xs font-semibold uppercase tracking-wider text-ink-500 hover:text-ink-950 hover:bg-ink-50 transition-colors">
                        <span class="flex items-center gap-2">
                            Category
                            @if(request('category'))
                            <span class="w-1.5 h-1.5 rounded-full bg-ink-950 inline-block"></span>
                            @endif
                        </span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 flex-shrink-0"
                             :class="open ? 'rotate-180' : ''"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="border-t border-ink-100 px-4 py-3 space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="category" value=""
                                   {{ !request('category') ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 text-ink-950 border-ink-300"
                                   onchange="this.form.submit()">
                            <span class="text-sm text-ink-700 group-hover:text-ink-950">All categories</span>
                        </label>
                        @foreach($categories as $cat)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="category" value="{{ $cat->id }}"
                                   {{ request('category') == $cat->id ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 text-ink-950 border-ink-300"
                                   onchange="this.form.submit()">
                            <span class="text-sm text-ink-700 group-hover:text-ink-950">{{ $cat->name }}</span>
                        </label>
                        @foreach($cat->children as $child)
                        <label class="flex items-center gap-2 cursor-pointer group pl-4">
                            <input type="radio" name="category" value="{{ $child->id }}"
                                   {{ request('category') == $child->id ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 text-ink-950 border-ink-300"
                                   onchange="this.form.submit()">
                            <span class="text-sm text-ink-500 group-hover:text-ink-950">{{ $child->name }}</span>
                        </label>
                        @endforeach
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- ── Sort by ── -->
                <div class="card overflow-hidden" x-data="{ open: {{ request('sort') && request('sort') !== 'latest' ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-3 text-xs font-semibold uppercase tracking-wider text-ink-500 hover:text-ink-950 hover:bg-ink-50 transition-colors">
                        <span class="flex items-center gap-2">
                            Sort by
                            @if(request('sort') && request('sort') !== 'latest')
                            <span class="w-1.5 h-1.5 rounded-full bg-ink-950 inline-block"></span>
                            @endif
                        </span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 flex-shrink-0"
                             :class="open ? 'rotate-180' : ''"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="border-t border-ink-100 px-4 py-3 space-y-2">
                        @foreach(['latest' => 'Latest', 'oldest' => 'Oldest', 'popular' => 'Most viewed', 'downloads' => 'Most downloaded'] as $val => $label)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="sort" value="{{ $val }}"
                                   {{ request('sort', 'latest') === $val ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 text-ink-950 border-ink-300"
                                   onchange="this.form.submit()">
                            <span class="text-sm text-ink-700 group-hover:text-ink-950">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- ── Tags ── -->
                @if($tags->isNotEmpty())
                <div class="card overflow-hidden" x-data="{ open: {{ request('tag') ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-3 text-xs font-semibold uppercase tracking-wider text-ink-500 hover:text-ink-950 hover:bg-ink-50 transition-colors">
                        <span class="flex items-center gap-2">
                            Tags
                            @if(request('tag'))
                            <span class="w-1.5 h-1.5 rounded-full bg-ink-950 inline-block"></span>
                            @endif
                        </span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 flex-shrink-0"
                             :class="open ? 'rotate-180' : ''"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="border-t border-ink-100 px-4 py-3">
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($tags->take(20) as $tag)
                            <a href="{{ route('data-room.index', array_merge(request()->query(), ['tag' => $tag->slug])) }}"
                               class="badge {{ request('tag') === $tag->slug ? 'badge-dark' : 'badge-outline' }} hover:bg-ink-100 transition-colors">
                                {{ $tag->name }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Clear filters -->
                @if(request()->hasAny(['search', 'type', 'category', 'tag', 'sort']))
                <a href="{{ route('data-room.index') }}" class="btn-ghost btn-sm w-full justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    Clear all filters
                </a>
                @endif

            </form>

            <!-- Mobile version of the filters (shown when toggled) -->
            <div x-data x-show="$store.mobileFilters?.open" x-cloak class="lg:hidden">
                {{-- mirrors desktop form, omitted for brevity - mobile uses the toggle above --}}
            </div>
        </aside>

        <!-- ── Artifact grid ──────────────────────────────────────────── -->
        <div class="flex-1 min-w-0">

            <!-- Back to categories -->
            @if(request('category') && $categories->isNotEmpty())
            <a href="{{ route('data-room.index') }}"
               class="inline-flex items-center gap-1.5 text-sm text-ink-500 hover:text-ink-950 transition-colors mb-4">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                All categories
            </a>
            @endif

            @if($artifacts->isEmpty())
            <div class="card p-12 text-center">
                <svg class="w-12 h-12 text-ink-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                <h3 class="text-base font-semibold text-ink-950 mb-1">No artifacts found</h3>
                <p class="text-sm text-ink-500">Try adjusting your filters or search query.</p>
                @if(auth()->user()->canPost())
                <a href="{{ route('data-room.create') }}" class="btn-primary btn-sm mt-5 inline-flex">Upload an artifact</a>
                @endif
            </div>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($artifacts as $artifact)
                <a href="{{ route('data-room.show', $artifact) }}" class="card-hover group flex flex-col">
                    @if($artifact->thumbnail_url)
                    <div class="relative h-36 overflow-hidden rounded-t-xl">
                        <img src="{{ $artifact->thumbnail_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="">
                        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-ink-950/60"></div>
                        <div class="absolute bottom-0 left-0 right-0 px-4 pb-3">
                            <span class="badge bg-white/15 text-white/90 text-[10px]">{{ $artifact->type_label }}</span>
                            <h3 class="text-sm font-semibold text-white mt-1.5 line-clamp-2 group-hover:underline underline-offset-2">{{ $artifact->title }}</h3>
                        </div>
                    </div>
                    @else
                    <div class="bg-ink-950 px-5 pt-5 pb-4">
                        <div class="flex items-start justify-between gap-3">
                            <span class="text-3xl leading-none">{{ $artifact->type_icon }}</span>
                            <span class="badge bg-white/10 text-white/80 text-[10px]">{{ $artifact->type_label }}</span>
                        </div>
                        <h3 class="text-sm font-semibold text-white mt-3 line-clamp-2 group-hover:underline underline-offset-2">{{ $artifact->title }}</h3>
                    </div>
                    @endif
                    <div class="p-4 flex flex-col flex-1">
                        @if($artifact->summary)
                        <p class="text-xs text-ink-600 line-clamp-2 flex-1 mb-3">{{ $artifact->summary }}</p>
                        @else
                        <div class="flex-1"></div>
                        @endif

                        @if($artifact->tags->isNotEmpty())
                        <div class="flex flex-wrap gap-1 mb-3">
                            @foreach($artifact->tags->take(3) as $tag)
                            <span class="badge-outline text-[10px]">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                        @endif

                        <div class="flex items-center justify-between text-[11px] text-ink-400 pt-3 border-t border-ink-100">
                            <span class="truncate mr-2">{{ $artifact->user->name }}</span>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ $artifact->views_count }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    {{ $artifact->downloads_count }}
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            @if($artifacts->hasPages())
            <div class="mt-8 flex justify-center">{{ $artifacts->links() }}</div>
            @endif
            @endif
        </div>
    </div>

    @endif
</x-app-layout>
