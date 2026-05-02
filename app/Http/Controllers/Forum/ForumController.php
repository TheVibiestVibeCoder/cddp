<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

    public function storeCategory(Request $request)
    {
        abort_if(!auth()->user()->canPost(), 403);

        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:forum_categories,name',
            'description' => 'nullable|string|max:500',
            'icon'        => 'nullable|string|max:10',
            'color'       => 'nullable|string|max:7',
        ]);

        ForumCategory::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'icon'        => $validated['icon'] ?: '💬',
            'color'       => $validated['color'] ?: '#18181b',
            'order'       => (ForumCategory::max('order') ?? 0) + 1,
        ]);

        return redirect()->route('forum.index')->with('success', 'Forum created.');
    }

    public function updateCategory(Request $request, ForumCategory $category)
    {
        abort_if(!auth()->user()->canPost(), 403);

        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:forum_categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'icon'        => 'nullable|string|max:10',
            'color'       => 'nullable|string|max:7',
        ]);

        $category->update([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'icon'        => $validated['icon'] ?: $category->icon,
            'color'       => $validated['color'] ?: $category->color,
        ]);

        return redirect()->route('forum.index')->with('success', 'Forum updated.');
    }
}
