<x-app-layout>
    <x-slot name="title">Edit — {{ $artifact->title }}</x-slot>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-ink-400 mb-6">
        <a href="{{ route('data-room.index') }}" class="hover:text-ink-950">Data Room</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        <a href="{{ route('data-room.show', $artifact) }}" class="hover:text-ink-950 truncate max-w-xs">{{ $artifact->title }}</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        <span class="text-ink-600">Edit</span>
    </nav>

    <div class="max-w-2xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-ink-950 tracking-tight">Edit Artifact</h1>
            <p class="text-sm text-ink-500 mt-0.5">Update the details for this artifact</p>
        </div>

        <form method="POST" action="{{ route('data-room.update', $artifact) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="card p-5 space-y-5">
                <h2 class="text-sm font-semibold text-ink-950 pb-2 border-b border-ink-100">Basic Information</h2>

                <div>
                    <label for="title" class="label">Title <span class="text-red-500">*</span></label>
                    <input id="title" type="text" name="title" value="{{ old('title', $artifact->title) }}"
                           class="input @error('title') input-error @enderror" required>
                    @error('title')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="type" class="label">Type <span class="text-red-500">*</span></label>
                    <select id="type" name="type" class="input @error('type') input-error @enderror" required>
                        @foreach(['document', 'report', 'brief', 'video', 'image', 'link', 'dataset', 'other'] as $t)
                        <option value="{{ $t }}" {{ old('type', $artifact->type) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                    @error('type')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="category_id" class="label">Category</label>
                    <select id="category_id" name="category_id" class="input">
                        <option value="">No category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $artifact->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="summary" class="label">Short summary</label>
                    <input id="summary" type="text" name="summary"
                           value="{{ old('summary', $artifact->summary) }}"
                           class="input" maxlength="500" placeholder="One-line description shown in listings">
                    @error('summary')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="label">Full description</label>
                    <textarea id="description" name="description" rows="5" class="input resize-y">{{ old('description', $artifact->description) }}</textarea>
                </div>
            </div>

            <!-- File / URL -->
            <div class="card p-5 space-y-5" x-data="{ hasFile: false, fileName: '' }">
                <h2 class="text-sm font-semibold text-ink-950 pb-2 border-b border-ink-100">Content</h2>

                @if($artifact->file_name)
                <div class="flex items-center gap-3 p-3 bg-ink-50 rounded-lg border border-ink-200 text-sm">
                    <svg class="w-5 h-5 text-ink-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <div class="min-w-0">
                        <p class="font-medium text-ink-950 truncate">{{ $artifact->file_name }}</p>
                        @if($artifact->file_size)<p class="text-xs text-ink-400">{{ $artifact->file_size }}</p>@endif
                    </div>
                </div>
                <div>
                    <label class="label">Replace file <span class="text-ink-400 normal-case tracking-normal font-normal">(optional)</span></label>
                    <div class="border-2 border-dashed border-ink-200 rounded-xl p-6 text-center hover:border-ink-400 transition-colors cursor-pointer"
                         x-on:click="$refs.fileInput.click()"
                         x-bind:class="hasFile ? 'border-ink-950 bg-ink-50' : ''">
                        <div x-show="!hasFile">
                            <p class="text-sm text-ink-600">Click to replace the existing file</p>
                            <p class="text-xs text-ink-400 mt-1">Max 100MB</p>
                        </div>
                        <div x-show="hasFile" class="flex items-center justify-center gap-2 text-sm text-ink-950">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span x-text="fileName" class="font-medium"></span>
                        </div>
                        <input type="file" name="file" class="hidden" x-ref="fileInput"
                               x-on:change="hasFile = !!$event.target.files[0]; fileName = $event.target.files[0]?.name ?? ''">
                    </div>
                </div>
                @else
                <div>
                    <label class="label">File upload</label>
                    <div class="border-2 border-dashed border-ink-200 rounded-xl p-8 text-center hover:border-ink-400 transition-colors cursor-pointer"
                         x-on:click="$refs.fileInput.click()"
                         x-bind:class="hasFile ? 'border-ink-950 bg-ink-50' : ''">
                        <div x-show="!hasFile">
                            <svg class="w-10 h-10 text-ink-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                            <p class="text-sm font-medium text-ink-700">Drop file here or click to browse</p>
                            <p class="text-xs text-ink-400 mt-1">Max 100MB</p>
                        </div>
                        <div x-show="hasFile" class="flex items-center justify-center gap-2 text-sm text-ink-950 font-medium">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span x-text="fileName"></span>
                        </div>
                        <input type="file" name="file" class="hidden" x-ref="fileInput"
                               x-on:change="hasFile = !!$event.target.files[0]; fileName = $event.target.files[0]?.name ?? ''">
                    </div>
                </div>
                @endif

                <div>
                    <label for="external_url" class="label">External URL</label>
                    <input id="external_url" type="url" name="external_url"
                           value="{{ old('external_url', $artifact->external_url) }}"
                           class="input @error('external_url') input-error @enderror" placeholder="https://…">
                    @error('external_url')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Thumbnail -->
            <div class="card p-5 space-y-4" x-data="{ thumbMode: 'file' }">
                <h2 class="text-sm font-semibold text-ink-950 pb-2 border-b border-ink-100">Preview Image <span class="text-ink-400 normal-case tracking-normal font-normal">(optional)</span></h2>
                @if($artifact->thumbnail_url)
                <div class="flex items-center gap-3">
                    <img src="{{ $artifact->thumbnail_url }}" class="w-20 h-14 object-cover rounded-lg border border-ink-200" alt="">
                    <span class="text-xs text-ink-500">Current preview image</span>
                </div>
                @endif
                <div class="flex gap-2">
                    <button type="button" @click="thumbMode = 'file'"
                            :class="thumbMode === 'file' ? 'bg-ink-950 text-white' : 'btn-secondary'"
                            class="px-3 py-1.5 text-xs rounded-lg font-medium transition-colors">Upload file</button>
                    <button type="button" @click="thumbMode = 'url'"
                            :class="thumbMode === 'url' ? 'bg-ink-950 text-white' : 'btn-secondary'"
                            class="px-3 py-1.5 text-xs rounded-lg font-medium transition-colors">Paste URL</button>
                </div>
                <div x-show="thumbMode === 'file'">
                    <input type="file" name="thumbnail_file" accept="image/*"
                           class="block w-full text-sm text-ink-600 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-ink-100 file:text-ink-700 hover:file:bg-ink-200">
                    <p class="mt-1.5 text-xs text-ink-400">Max 10MB &middot; JPG, PNG, WebP, GIF</p>
                </div>
                <div x-show="thumbMode === 'url'" x-cloak>
                    <input type="url" name="thumbnail_url" value="{{ old('thumbnail_url') }}"
                           class="input @error('thumbnail_url') input-error @enderror" placeholder="https://…">
                </div>
                @error('thumbnail_file')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                @error('thumbnail_url')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Metadata -->
            <div class="card p-5 space-y-5">
                <h2 class="text-sm font-semibold text-ink-950 pb-2 border-b border-ink-100">Metadata</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="source" class="label">Source</label>
                        <input id="source" type="text" name="source"
                               value="{{ old('source', $artifact->source) }}"
                               class="input" placeholder="Publication, author, org…">
                    </div>
                    <div>
                        <label for="published_date" class="label">Published date</label>
                        <input id="published_date" type="date" name="published_date"
                               value="{{ old('published_date', $artifact->published_date?->format('Y-m-d')) }}"
                               class="input">
                    </div>
                    <div>
                        <label for="language" class="label">Language</label>
                        <input id="language" type="text" name="language"
                               value="{{ old('language', $artifact->language) }}"
                               class="input" placeholder="en, de, fr…" maxlength="10">
                    </div>
                </div>

                <!-- Tags -->
                @if($tags->isNotEmpty())
                <div>
                    <label class="label">Tags</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tags as $tag)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                   class="sr-only peer"
                                   {{ in_array($tag->id, old('tags', $artifact->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <span class="badge-outline peer-checked:bg-ink-950 peer-checked:text-white peer-checked:border-ink-950 hover:bg-ink-100 transition-colors cursor-pointer">
                                {{ $tag->name }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <div>
                    <label for="new_tags" class="label">Add new tags</label>
                    <input id="new_tags" type="text" name="new_tags" value="{{ old('new_tags') }}"
                           class="input" placeholder="comma-separated">
                </div>

                {{-- Featured toggle: admin only --}}
                @if(auth()->user()->isAdmin())
                <div class="pt-2 border-t border-ink-100">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1"
                               class="w-4 h-4 rounded border-ink-300 text-ink-950 focus:ring-ink-500"
                               {{ old('is_featured', $artifact->is_featured) ? 'checked' : '' }}>
                        <div>
                            <span class="text-sm font-medium text-ink-950 group-hover:underline">Feature on dashboard</span>
                            <p class="text-xs text-ink-400">Highlights this artifact in the Featured section</p>
                        </div>
                        <span class="ml-auto badge-dark text-[10px]">Admin only</span>
                    </label>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('data-room.show', $artifact) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    Save changes
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
