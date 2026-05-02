<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumThread;

class ForumController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::withCount('threads')
            ->with(['latestThread.user'])
            ->orderBy('order')
            ->get();

        $recentThreads = ForumThread::with(['user', 'category'])
            ->latest()
            ->take(10)
            ->get();

        return view('forum.index', compact('categories', 'recentThreads'));
    }

    public function category(ForumCategory $category)
    {
        $threads = $category->threads()
            ->with(['user', 'lastReplyUser', 'tags'])
            ->paginate(20);

        return view('forum.category', compact('category', 'threads'));
    }
}
