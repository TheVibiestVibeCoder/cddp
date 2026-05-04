<div class="px-5 py-4" id="comment-{{ $comment->id }}" x-data="{ editing: false }">
    <div class="flex items-start gap-3">
        <img src="{{ $comment->user->avatar_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0 border border-ink-200" alt="">
        <div class="flex-1 min-w-0">
            <div class="flex items-baseline gap-2 flex-wrap">
                <span class="text-sm font-semibold text-ink-950">{{ $comment->user->name }}</span>
                <span class="text-xs text-ink-400">{{ $comment->created_at->diffForHumans() }}</span>
            </div>

            <!-- Read view -->
            <div x-show="!editing" class="text-sm text-ink-700 mt-1 leading-relaxed">{{ $comment->body }}</div>

            <!-- Inline edit form -->
            <form x-show="editing" x-cloak method="POST"
                  action="{{ route('data-room.comments.update', $comment) }}" class="mt-1">
                @csrf @method('PUT')
                <textarea name="body" rows="3"
                          class="input resize-y w-full text-sm">{{ $comment->body }}</textarea>
                <div class="mt-1.5 flex items-center justify-end gap-2">
                    <button type="button" @click="editing = false" class="btn-secondary btn-sm">Cancel</button>
                    <button type="submit" class="btn-primary btn-sm">Save</button>
                </div>
            </form>

            <div class="flex items-center gap-3 mt-2" x-show="!editing">
                @if(auth()->user()->canPost())
                <button type="button" class="text-xs text-ink-400 hover:text-ink-950 transition-colors"
                        x-data x-on:click="$dispatch('reply-to', { parentId: {{ $comment->id }}, name: '{{ addslashes($comment->user->name) }}' })">
                    Reply
                </button>
                @endif
                @if(auth()->user()->isAdmin() || $comment->user_id === auth()->id())
                <button type="button" @click="editing = true"
                        class="text-xs text-ink-400 hover:text-ink-950 transition-colors">Edit</button>
                <form method="POST" action="{{ route('data-room.comments.destroy', $comment) }}"
                      onsubmit="return confirm('Delete this comment?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 transition-colors">Delete</button>
                </form>
                @endif
            </div>

            <!-- Replies -->
            @if($comment->replies->isNotEmpty())
            <div class="mt-3 space-y-3 pl-4 border-l-2 border-ink-100" x-show="!editing">
                @foreach($comment->replies as $reply)
                <div class="flex items-start gap-3" x-data="{ editing: false }">
                    <img src="{{ $reply->user->avatar_url }}" class="w-6 h-6 rounded-full object-cover flex-shrink-0" alt="">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline gap-2">
                            <span class="text-xs font-semibold text-ink-950">{{ $reply->user->name }}</span>
                            <span class="text-xs text-ink-400">{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        <p x-show="!editing" class="text-sm text-ink-700 mt-0.5">{{ $reply->body }}</p>
                        <form x-show="editing" x-cloak method="POST"
                              action="{{ route('data-room.comments.update', $reply) }}" class="mt-1">
                            @csrf @method('PUT')
                            <textarea name="body" rows="2"
                                      class="input resize-y w-full text-sm">{{ $reply->body }}</textarea>
                            <div class="mt-1 flex items-center justify-end gap-2">
                                <button type="button" @click="editing = false" class="btn-secondary btn-sm">Cancel</button>
                                <button type="submit" class="btn-primary btn-sm">Save</button>
                            </div>
                        </form>
                        @if(auth()->user()->isAdmin() || $reply->user_id === auth()->id())
                        <div class="flex items-center gap-3 mt-1" x-show="!editing">
                            <button type="button" @click="editing = true"
                                    class="text-[11px] text-ink-400 hover:text-ink-950 transition-colors">Edit</button>
                            <form method="POST" action="{{ route('data-room.comments.destroy', $reply) }}"
                                  onsubmit="return confirm('Delete this reply?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[11px] text-red-500 hover:text-red-700 transition-colors">Delete</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
