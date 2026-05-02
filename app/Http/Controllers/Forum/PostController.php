<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumThread;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request, ForumCategory $category, ForumThread $thread)
    {
        abort_if(!auth()->user()->canPost(), 403);
        abort_if($thread->is_locked && !auth()->user()->isAdmin(), 403);

        $validated = $request->validate([
            'body'      => 'required|string|min:2',
            'parent_id' => 'nullable|exists:forum_posts,id',
        ]);

        ForumPost::create([
            'body'            => $validated['body'],
            'user_id'         => auth()->id(),
            'forum_thread_id' => $thread->id,
            'parent_id'       => $validated['parent_id'] ?? null,
        ]);

        $thread->increment('replies_count');
        $thread->update([
            'last_reply_at'      => now(),
            'last_reply_user_id' => auth()->id(),
        ]);
        $category->increment('posts_count');

        return redirect()->route('forum.thread', [$category, $thread])
            ->with('success', 'Reply posted.');
    }

    public function update(Request $request, ForumPost $post)
    {
        abort_if(!auth()->user()->isAdmin() && $post->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'body' => 'required|string|min:2',
        ]);

        $post->update(['body' => $validated['body']]);

        return back()->with('success', 'Post updated.');
    }

    public function destroy(ForumPost $post)
    {
        abort_if(!auth()->user()->isAdmin() && $post->user_id !== auth()->id(), 403);
        $post->thread->decrement('replies_count');
        $post->delete();
        return back()->with('success', 'Post deleted.');
    }
}
