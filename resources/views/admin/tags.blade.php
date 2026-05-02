<x-app-layout>
    <x-slot name="title">Tags</x-slot>

    <nav class="flex items-center gap-2 text-xs text-ink-400 mb-6">
        <a href="{{ route('admin.index') }}" class="hover:text-ink-950">Admin</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        <span class="text-ink-600">Tags</span>
    </nav>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Tag list -->
        <div class="xl:col-span-2">
            <h1 class="text-2xl font-bold text-ink-950 tracking-tight mb-4">Tags</h1>
            @if($tags->isEmpty())
            <div class="card p-10 text-center text-sm text-ink-400">No tags yet.</div>
            @else
            <div class="card">
                <div class="flex flex-wrap gap-2 p-5">
                    @foreach($tags as $tag)
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-ink-50 border border-ink-200 rounded-full text-sm">
                        <span class="text-ink-800">{{ $tag->name }}</span>
                        <span class="text-xs text-ink-400">({{ $tag->artifacts_count + $tag->threads_count }})</span>
                        <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}"
                              onsubmit="return confirm('Delete tag \'{{ $tag->name }}\'?')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-ink-400 hover:text-red-600 transition-colors ml-0.5">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Add tag -->
        <div>
            <h2 class="text-sm font-semibold text-ink-950 mb-4">Add Tag</h2>
            <div class="card p-5">
                <form method="POST" action="{{ route('admin.tags.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="label">Tag name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="input @error('name') input-error @enderror" required placeholder="e.g. propaganda">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn-primary w-full justify-center">Add Tag</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
