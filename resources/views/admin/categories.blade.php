<x-app-layout>
    <x-slot name="title">Categories</x-slot>

    <nav class="flex items-center gap-2 text-xs text-ink-400 mb-6">
        <a href="{{ route('admin.index') }}" class="hover:text-ink-950">Admin</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        <span class="text-ink-600">Categories</span>
    </nav>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        <!-- Category list -->
        <div class="xl:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-ink-950 tracking-tight">Categories</h1>
            </div>

            @if($categories->isEmpty())
            <div class="card p-10 text-center text-sm text-ink-400">No categories yet.</div>
            @else
            <div class="card divide-y divide-ink-100">
                @foreach($categories as $cat)
                <div x-data="{ editing: false }">

                    <!-- Parent row -->
                    <div class="px-4 py-3.5 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            @if($cat->cover_image_url)
                            <img src="{{ $cat->cover_image_url }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0" alt="">
                            @endif
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-ink-950">
                                    @if($cat->icon)<span class="mr-1">{{ $cat->icon }}</span>@endif
                                    {{ $cat->name }}
                                </p>
                                @if($cat->description)
                                <p class="text-xs text-ink-500 mt-0.5 truncate">{{ $cat->description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="badge-default text-[10px]">{{ $cat->artifacts_count }} artifacts</span>
                            <button @click="editing = !editing" class="btn-ghost btn-sm" :class="editing ? 'bg-ink-100 text-ink-950' : 'text-ink-500'">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}"
                                  onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-ghost btn-sm text-red-600 hover:bg-red-50 hover:text-red-700">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Inline edit form (parent) -->
                    <div x-show="editing" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="border-t border-ink-100 bg-ink-50 px-4 py-4">
                        <form method="POST" action="{{ route('admin.categories.update', $cat) }}"
                              enctype="multipart/form-data" class="space-y-3">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="label">Name</label>
                                    <input type="text" name="name" value="{{ $cat->name }}" class="input" required>
                                </div>
                                <div>
                                    <label class="label">Icon (emoji)</label>
                                    <input type="text" name="icon" value="{{ $cat->icon }}" class="input" placeholder="📁" maxlength="10">
                                </div>
                            </div>
                            <div>
                                <label class="label">Description</label>
                                <textarea name="description" rows="2" class="input resize-none">{{ $cat->description }}</textarea>
                            </div>
                            <div>
                                <label class="label">Cover image</label>
                                @if($cat->cover_image_url)
                                <div class="mb-2 flex items-center gap-3">
                                    <img src="{{ $cat->cover_image_url }}" class="h-14 rounded-lg object-cover" alt="">
                                    <p class="text-xs text-ink-400">Upload a new image to replace the current one</p>
                                </div>
                                @endif
                                <input type="file" name="cover_image" accept="image/*" class="input py-1.5 text-sm">
                            </div>
                            <div class="flex items-center gap-2 pt-1">
                                <button type="submit" class="btn-primary btn-sm">Save changes</button>
                                <button type="button" @click="editing = false" class="btn-ghost btn-sm">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <!-- Children -->
                    @if($cat->children->isNotEmpty())
                    <div class="border-t border-ink-100 divide-y divide-ink-100">
                        @foreach($cat->children as $child)
                        <div x-data="{ editing: false }">
                            <div class="pl-8 pr-4 py-2.5 flex items-center justify-between gap-4 bg-ink-50/40">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    @if($child->cover_image_url)
                                    <img src="{{ $child->cover_image_url }}" class="w-7 h-7 rounded object-cover flex-shrink-0" alt="">
                                    @endif
                                    <p class="text-xs text-ink-700">
                                        @if($child->icon)<span class="mr-1">{{ $child->icon }}</span>@endif
                                        {{ $child->name }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button @click="editing = !editing"
                                            class="text-xs text-ink-400 hover:text-ink-950 transition-colors"
                                            :class="editing ? 'text-ink-950 font-medium' : ''">Edit</button>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $child) }}"
                                          onsubmit="return confirm('Delete subcategory?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                                    </form>
                                </div>
                            </div>
                            <div x-show="editing" x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="pl-8 pr-4 py-4 bg-ink-50 border-t border-ink-100">
                                <form method="POST" action="{{ route('admin.categories.update', $child) }}"
                                      enctype="multipart/form-data" class="space-y-3">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="label">Name</label>
                                            <input type="text" name="name" value="{{ $child->name }}" class="input" required>
                                        </div>
                                        <div>
                                            <label class="label">Icon (emoji)</label>
                                            <input type="text" name="icon" value="{{ $child->icon }}" class="input" placeholder="📁" maxlength="10">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="label">Description</label>
                                        <textarea name="description" rows="2" class="input resize-none">{{ $child->description }}</textarea>
                                    </div>
                                    <div>
                                        <label class="label">Cover image</label>
                                        @if($child->cover_image_url)
                                        <div class="mb-2 flex items-center gap-3">
                                            <img src="{{ $child->cover_image_url }}" class="h-14 rounded-lg object-cover" alt="">
                                            <p class="text-xs text-ink-400">Upload a new image to replace</p>
                                        </div>
                                        @endif
                                        <input type="file" name="cover_image" accept="image/*" class="input py-1.5 text-sm">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="submit" class="btn-primary btn-sm">Save</button>
                                        <button type="button" @click="editing = false" class="btn-ghost btn-sm">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Add category -->
        <div>
            <h2 class="text-sm font-semibold text-ink-950 mb-4">Add Category</h2>
            <div class="card p-5">
                <form method="POST" action="{{ route('admin.categories.store') }}"
                      enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="label">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="input @error('name') input-error @enderror" required placeholder="Category name">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Description</label>
                        <textarea name="description" rows="2" class="input resize-none" placeholder="Optional description">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="label">Parent category</label>
                        <select name="parent_id" class="input">
                            <option value="">Top level</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Icon (emoji)</label>
                        <input type="text" name="icon" value="{{ old('icon') }}" class="input" placeholder="📁" maxlength="10">
                    </div>
                    <div>
                        <label class="label">Cover image</label>
                        <input type="file" name="cover_image" accept="image/*" class="input py-1.5 text-sm">
                        <p class="mt-1 text-xs text-ink-400">Displayed on the category card in the library</p>
                    </div>
                    <button type="submit" class="btn-primary w-full justify-center">Add Category</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
