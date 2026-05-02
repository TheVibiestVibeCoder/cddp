<div class="px-5 py-4" id="comment-{{ $comment->id }}">
    <div class="flex items-start gap-3">
        <img src="{{ $comment->user->avatar_url }}" class="w-8 h-8 rounded-full flex-shrink-0 border border-ink-200" alt="">
        <div class="flex-1 min-w-0">
            <div class="flex items-baseline gap-2 flex-wrap">
                <span class="text-sm font-semibold text-ink-950">{{ $comment->user->name }}</span>
                <span class="text-xs text-ink-400">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <div class="text-sm text-ink-700 mt-1 leading-relaxed">{{ $comment->body }}</div>

            <div class="flex items-center gap-3 mt-2">
                @if(auth()->user()->canPost())
                <button type="button" class="text-xs text-ink-400 hover:text-ink-950 transition-colors"
                        x-data x-on:click="$dispatch('reply-to', { parentId: {{ $comment->id }}, name: '{{ $comment->user->name }}' })">
                    Reply
                </button>
                @endif
                @if(auth()->user()->isAdmin() || $comment->user_id === auth()->id())
                <form method="POST" action="{{ route('data-room.comments.destroy', $comment) }}"
                      onsubmit="return confirm('Delete this comment?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 transition-colors">Delete</button>
                </form>
                @endif
            </div>

            <!-- Replies -->
            @if($comment->replies->isNotEmpty())
            <div class="mt-3 space-y-3 pl-4 border-l-2 border-ink-100">
                @foreach($comment->replies as $reply)
                <div class="flex items-start gap-3">
                    <img src="{{ $reply->user->avatar_url }}" class="w-6 h-6 rounded-full flex-shrink-0" alt="">
                    <div class="flex-1">
                        <div class="flex items-baseline gap-2">
                            <span class="text-xs font-semibold text-ink-950">{{ $reply->user->name }}</span>
                            <span class="text-xs text-ink-400">{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-ink-700 mt-0.5">{{ $reply->body }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
