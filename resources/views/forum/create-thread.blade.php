<x-app-layout>
    <x-slot name="title">New Thread — {{ $category->name }}</x-slot>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-ink-400 mb-6">
        <a href="{{ route('forum.index') }}" class="hover:text-ink-950">Forum</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        <a href="{{ route('forum.category', $category) }}" class="hover:text-ink-950">{{ $category->name }}</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        <span class="text-ink-600">New Thread</span>
    </nav>

    <div class="max-w-2xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-ink-950 tracking-tight">New Thread</h1>
            <p class="text-sm text-ink-500 mt-0.5">Posting in <span class="font-medium text-ink-700">{{ $category->name }}</span></p>
        </div>

        <form method="POST" action="{{ route('forum.thread.store', $category) }}" class="space-y-6">
            @csrf

            <div class="card p-5 space-y-5">
                <div>
                    <label for="title" class="label">Thread title <span class="text-red-500">*</span></label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}"
                           class="input @error('title') input-error @enderror text-base font-medium"
                           required placeholder="What would you like to discuss?">
                    @error('title')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="body" class="label">Content <span class="text-red-500">*</span></label>
                    <textarea id="body" name="body" rows="10"
                              class="input @error('body') input-error @enderror resize-y font-mono text-sm"
                              required placeholder="Write your post here…">{{ old('body') }}</textarea>
                    <p class="text-xs text-ink-400 mt-1.5">You can use @username to mention users and reference artifacts</p>
                    @error('body')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Tags -->
            @if($tags->isNotEmpty())
            <div class="card p-5 space-y-4">
                <h2 class="text-sm font-semibold text-ink-950 pb-2 border-b border-ink-100">Tags</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                               class="sr-only peer" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                        <span class="badge-outline peer-checked:bg-ink-950 peer-checked:text-white peer-checked:border-ink-950 hover:bg-ink-100 transition-colors cursor-pointer">
                            {{ $tag->name }}
                        </span>
                    </label>
                    @endforeach
                </div>
                <div>
                    <label for="new_tags" class="label">Add new tags</label>
                    <input id="new_tags" type="text" name="new_tags" value="{{ old('new_tags') }}"
                           class="input" placeholder="comma-separated">
                </div>
            </div>
            @endif

            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('forum.category', $category) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                    Post Thread
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
