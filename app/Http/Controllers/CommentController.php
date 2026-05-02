<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Artifact $artifact)
    {
        abort_if(!auth()->user()->canPost(), 403);

        $validated = $request->validate([
            'body'      => 'required|string|min:2|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        Comment::create([
            'body'             => $validated['body'],
            'user_id'          => auth()->id(),
            'commentable_type' => Artifact::class,
            'commentable_id'   => $artifact->id,
            'parent_id'        => $validated['parent_id'] ?? null,
        ]);

        return back()->with('success', 'Comment added.');
    }

    public function destroy(Comment $comment)
    {
        abort_if(!auth()->user()->isAdmin() && $comment->user_id !== auth()->id(), 403);
        $comment->delete();
        return back()->with('success', 'Comment deleted.');
    }
}
