<x-app-layout>
    <x-slot name="title">Edit — {{ $thread->title }}</x-slot>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-ink-400 mb-6">
        <a href="{{ route('forum.index') }}" class="hover:text-ink-950">Forum</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        <a href="{{ route('forum.category', $thread->category) }}" class="hover:text-ink-950">{{ $thread->category->name }}</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        <a href="{{ route('forum.thread', [$thread->category, $thread]) }}" class="hover:text-ink-950 truncate max-w-xs">{{ $thread->title }}</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        <span class="text-ink-600">Edit</span>
    </nav>

    <div class="max-w-2xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-ink-950 tracking-tight">Edit Thread</h1>
            <p class="text-sm text-ink-500 mt-0.5">In <span class="font-medium text-ink-700">{{ $thread->category->name }}</span></p>
        </div>

        <form method="POST" action="{{ route('forum.thread.update', $thread) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="card p-5 space-y-5">
                <div>
                    <label for="title" class="label">Thread title <span class="text-red-500">*</span></label>
                    <input id="title" type="text" name="title"
                           value="{{ old('title', $thread->title) }}"
                           class="input @error('title') input-error @enderror text-base font-medium"
                           required>
                    @error('title')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="body" class="label">Content <span class="text-red-500">*</span></label>
                    <textarea id="body" name="body" rows="12"
                              class="input @error('body') input-error @enderror resize-y font-mono text-sm"
                              required>{{ old('body', $thread->body) }}</textarea>
                    @error('body')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Cover image -->
            <div class="card p-5 space-y-4" x-data="{ coverMode: 'file' }">
                <h2 class="text-sm font-semibold text-ink-950 pb-2 border-b border-ink-100">Cover Image <span class="text-ink-400 normal-case tracking-normal font-normal">(optional)</span></h2>
                @if($thread->cover_image_url)
                <div class="flex items-center gap-3">
                    <img src="{{ $thread->cover_image_url }}" class="w-24 h-14 object-cover rounded-lg border border-ink-200" alt="">
                    <span class="text-xs text-ink-500">Current cover image</span>
                </div>
                @endif
                <div class="flex gap-2">
                    <button type="button" @click="coverMode = 'file'"
                            :class="coverMode === 'file' ? 'bg-ink-950 text-white' : 'btn-secondary'"
                            class="px-3 py-1.5 text-xs rounded-lg font-medium transition-colors">Upload file</button>
                    <button type="button" @click="coverMode = 'url'"
                            :class="coverMode === 'url' ? 'bg-ink-950 text-white' : 'btn-secondary'"
                            class="px-3 py-1.5 text-xs rounded-lg font-medium transition-colors">Paste URL</button>
                </div>
                <div x-show="coverMode === 'file'">
                    <input type="file" name="cover_image_file" accept="image/*"
                           class="block w-full text-sm text-ink-600 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-ink-100 file:text-ink-700 hover:file:bg-ink-200">
                    <p class="mt-1.5 text-xs text-ink-400">Max 10MB &middot; JPG, PNG, WebP, GIF</p>
                </div>
                <div x-show="coverMode === 'url'" x-cloak>
                    <input type="url" name="cover_image_url" value="{{ old('cover_image_url') }}"
                           class="input @error('cover_image_url') input-error @enderror" placeholder="https://…">
                </div>
                @error('cover_image_file')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                @error('cover_image_url')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Tags -->
            @if($tags->isNotEmpty())
            <div class="card p-5 space-y-4">
                <h2 class="text-sm font-semibold text-ink-950 pb-2 border-b border-ink-100">Tags</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                               class="sr-only peer"
                               {{ in_array($tag->id, old('tags', $thread->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
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

            <!-- Admin controls -->
            @if(auth()->user()->isAdmin())
            <div class="card p-5 space-y-3">
                <h2 class="text-sm font-semibold text-ink-950 pb-2 border-b border-ink-100">Moderation <span class="ml-2 badge-dark text-[10px]">Admin only</span></h2>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" name="is_pinned" value="1"
                           class="w-4 h-4 rounded border-ink-300 text-ink-950 focus:ring-ink-500"
                           {{ old('is_pinned', $thread->is_pinned) ? 'checked' : '' }}>
                    <div>
                        <span class="text-sm font-medium text-ink-950 group-hover:underline">Pin thread</span>
                        <p class="text-xs text-ink-400">Always appears at the top of the category</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" name="is_locked" value="1"
                           class="w-4 h-4 rounded border-ink-300 text-ink-950 focus:ring-ink-500"
                           {{ old('is_locked', $thread->is_locked) ? 'checked' : '' }}>
                    <div>
                        <span class="text-sm font-medium text-ink-950 group-hover:underline">Lock thread</span>
                        <p class="text-xs text-ink-400">Prevents new replies from non-admins</p>
                    </div>
                </label>
            </div>
            @endif

            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('forum.thread', [$thread->category, $thread]) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    Save changes
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
