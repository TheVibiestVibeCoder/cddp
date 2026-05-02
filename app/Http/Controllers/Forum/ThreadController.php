<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumPost;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ThreadController extends Controller
{
    public function show(ForumCategory $category, ForumThread $thread)
    {
        abort_if($thread->forum_category_id !== $category->id, 404);

        $thread->incrementViews();
        $thread->load(['user', 'category', 'tags']);

        $posts = $thread->posts()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->paginate(20);

        return view('forum.thread', compact('category', 'thread', 'posts'));
    }

    public function create(ForumCategory $category)
    {
        abort_if(!auth()->user()->canPost(), 403);
        $tags = Tag::orderBy('name')->get();
        return view('forum.create-thread', compact('category', 'tags'));
    }

    public function store(Request $request, ForumCategory $category)
    {
        abort_if(!auth()->user()->canPost(), 403);

        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'body'     => 'required|string|min:10',
            'tags'     => 'nullable|array',
            'tags.*'   => 'exists:tags,id',
            'new_tags' => 'nullable|string',
        ]);

        $thread = ForumThread::create([
            'title'             => $validated['title'],
            'slug'              => Str::slug($validated['title']) . '-' . Str::random(6),
            'body'              => $validated['body'],
            'user_id'           => auth()->id(),
            'forum_category_id' => $category->id,
            'last_reply_at'     => now(),
            'last_reply_user_id'=> auth()->id(),
        ]);

        $tagIds = $validated['tags'] ?? [];
        if (!empty($validated['new_tags'])) {
            foreach (explode(',', $validated['new_tags']) as $tagName) {
                $tagName = trim($tagName);
                if ($tagName) {
                    $tag = Tag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName, 'slug' => Str::slug($tagName)]
                    );
                    $tagIds[] = $tag->id;
                }
            }
        }
        $thread->tags()->sync($tagIds);

        $category->increment('threads_count');

        return redirect()->route('forum.thread', [$category, $thread])
            ->with('success', 'Thread created.');
    }

    public function edit(ForumThread $thread)
    {
        abort_if(!auth()->user()->isAdmin() && $thread->user_id !== auth()->id(), 403);
        $tags = Tag::orderBy('name')->get();
        return view('forum.edit-thread', compact('thread', 'tags'));
    }

    public function update(Request $request, ForumThread $thread)
    {
        abort_if(!auth()->user()->isAdmin() && $thread->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'body'      => 'required|string|min:10',
            'tags'      => 'nullable|array',
            'tags.*'    => 'exists:tags,id',
            'new_tags'  => 'nullable|string',
            'is_pinned' => 'boolean',
            'is_locked' => 'boolean',
        ]);

        $thread->update([
            'title'     => $validated['title'],
            'body'      => $validated['body'],
            'is_pinned' => auth()->user()->isAdmin() ? $request->boolean('is_pinned') : $thread->is_pinned,
            'is_locked' => auth()->user()->isAdmin() ? $request->boolean('is_locked') : $thread->is_locked,
        ]);

        $tagIds = $validated['tags'] ?? [];
        if (!empty($validated['new_tags'])) {
            foreach (explode(',', $validated['new_tags']) as $tagName) {
                $tagName = trim($tagName);
                if ($tagName) {
                    $tag = Tag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName, 'slug' => Str::slug($tagName)]
                    );
                    $tagIds[] = $tag->id;
                }
            }
        }
        $thread->tags()->sync($tagIds);

        return redirect()->route('forum.thread', [$thread->category, $thread])
            ->with('success', 'Thread updated.');
    }

    public function destroy(ForumThread $thread)
    {
        abort_if(!auth()->user()->isAdmin() && $thread->user_id !== auth()->id(), 403);
        $thread->category->decrement('threads_count');
        $thread->delete();
        return redirect()->route('forum.category', $thread->category)->with('success', 'Thread deleted.');
    }
}
