<?php

namespace App\Http\Controllers\DataRoom;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ArtifactController extends Controller
{
    public function index(Request $request)
    {
        $query = Artifact::with(['user', 'category', 'tags'])
            ->where('is_published', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('slug', $request->tag));
        }

        $sort = $request->get('sort', 'latest');
        match($sort) {
            'popular' => $query->orderByDesc('views_count'),
            'downloads' => $query->orderByDesc('downloads_count'),
            'oldest' => $query->orderBy('created_at'),
            default => $query->orderByDesc('created_at'),
        };

        $artifacts = $query->paginate(12)->withQueryString();
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('order')->get();
        $tags = Tag::orderBy('name')->get();
        $types = ['document', 'report', 'brief', 'video', 'image', 'link', 'dataset', 'other'];

        return view('data-room.index', compact('artifacts', 'categories', 'tags', 'types'));
    }

    public function show(Artifact $artifact)
    {
        abort_if(!$artifact->is_published && !auth()->user()?->isAdmin(), 404);

        $artifact->incrementViews();
        $artifact->load(['user', 'category', 'tags', 'comments.user', 'comments.replies.user']);

        $related = Artifact::with(['user', 'category'])
            ->where('is_published', true)
            ->where('id', '!=', $artifact->id)
            ->where(function ($q) use ($artifact) {
                $q->where('category_id', $artifact->category_id)
                  ->orWhere('type', $artifact->type);
            })
            ->latest()
            ->take(4)
            ->get();

        return view('data-room.show', compact('artifact', 'related'));
    }

    public function create()
    {
        abort_if(!auth()->user()->canPost(), 403);
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('data-room.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->canPost(), 403);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'summary'        => 'nullable|string|max:500',
            'description'    => 'nullable|string',
            'type'           => 'required|in:document,video,image,link,report,brief,dataset,other',
            'category_id'    => 'nullable|exists:categories,id',
            'file'           => 'nullable|file|max:102400',
            'external_url'   => 'nullable|url',
            'thumbnail_file' => 'nullable|image|max:10240',
            'thumbnail_url'  => 'nullable|url|max:2048',
            'language'       => 'nullable|string|max:10',
            'source'         => 'nullable|string|max:255',
            'published_date' => 'nullable|date',
            'tags'           => 'nullable|array',
            'tags.*'         => 'exists:tags,id',
            'new_tags'       => 'nullable|string',
            'is_featured'    => 'boolean',
        ]);

        $thumbnail = null;
        if ($request->hasFile('thumbnail_file')) {
            Storage::disk('public')->makeDirectory('thumbnails');
            $thumbnail = $request->file('thumbnail_file')->store('thumbnails', 'public') ?: null;
        } elseif ($request->filled('thumbnail_url')) {
            $thumbnail = $request->input('thumbnail_url');
        }

        $filePath = null;
        $fileName = null;
        $fileSize = null;
        $fileMime = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            Storage::disk('public')->makeDirectory('artifacts');
            try {
                $filePath = $file->store('artifacts', 'public');
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['file' => 'File upload failed. Check server storage permissions and try again.']);
            }
            if (!$filePath) {
                return back()->withInput()->withErrors(['file' => 'File could not be saved. Check server storage permissions.']);
            }
            $fileName = $file->getClientOriginalName();
            $fileSize = $this->formatFileSize($file->getSize());
            $fileMime = $file->getMimeType();
        }

        $artifact = Artifact::create([
            'title'          => $validated['title'],
            'slug'           => Str::slug($validated['title']) . '-' . Str::random(6),
            'summary'        => $validated['summary'] ?? null,
            'description'    => $validated['description'] ?? null,
            'type'           => $validated['type'],
            'file_path'      => $filePath,
            'file_name'      => $fileName,
            'file_size'      => $fileSize,
            'file_mime'      => $fileMime,
            'external_url'   => $validated['external_url'] ?? null,
            'thumbnail'      => $thumbnail,
            'user_id'        => auth()->id(),
            'category_id'    => $validated['category_id'] ?? null,
            'language'       => $validated['language'] ?? 'en',
            'source'         => $validated['source'] ?? null,
            'published_date' => $validated['published_date'] ?? null,
            'is_featured'    => $request->boolean('is_featured'),
            'is_published'   => true,
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

        $artifact->tags()->sync($tagIds);

        return redirect()->route('data-room.show', $artifact)
            ->with('success', 'Artifact uploaded successfully.');
    }

    public function edit(Artifact $artifact)
    {
        abort_if(!auth()->user()->isAdmin() && $artifact->user_id !== auth()->id(), 403);
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('data-room.edit', compact('artifact', 'categories', 'tags'));
    }

    public function update(Request $request, Artifact $artifact)
    {
        abort_if(!auth()->user()->isAdmin() && $artifact->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'summary'        => 'nullable|string|max:500',
            'description'    => 'nullable|string',
            'type'           => 'required|in:document,video,image,link,report,brief,dataset,other',
            'category_id'    => 'nullable|exists:categories,id',
            'file'           => 'nullable|file|max:102400',
            'external_url'   => 'nullable|url',
            'thumbnail_file' => 'nullable|image|max:10240',
            'thumbnail_url'  => 'nullable|url|max:2048',
            'language'       => 'nullable|string|max:10',
            'source'         => 'nullable|string|max:255',
            'published_date' => 'nullable|date',
            'tags'           => 'nullable|array',
            'tags.*'         => 'exists:tags,id',
            'new_tags'       => 'nullable|string',
            'is_featured'    => 'boolean',
        ]);

        $thumbnail = $artifact->thumbnail;
        if ($request->hasFile('thumbnail_file')) {
            if ($artifact->thumbnail && !str_starts_with($artifact->thumbnail, 'http')) {
                Storage::disk('public')->delete($artifact->thumbnail);
            }
            Storage::disk('public')->makeDirectory('thumbnails');
            $thumbnail = $request->file('thumbnail_file')->store('thumbnails', 'public') ?: $artifact->thumbnail;
        } elseif ($request->filled('thumbnail_url')) {
            $thumbnail = $request->input('thumbnail_url');
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            Storage::disk('public')->makeDirectory('artifacts');
            try {
                $newPath = $file->store('artifacts', 'public');
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['file' => 'File upload failed. Check server storage permissions and try again.']);
            }
            if (!$newPath) {
                return back()->withInput()->withErrors(['file' => 'File could not be saved. Check server storage permissions.']);
            }
            if ($artifact->file_path) {
                Storage::disk('public')->delete($artifact->file_path);
            }
            $validated['file_path'] = $newPath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $this->formatFileSize($file->getSize());
            $validated['file_mime'] = $file->getMimeType();
        }

        $artifact->update([
            'title'          => $validated['title'],
            'summary'        => $validated['summary'] ?? null,
            'description'    => $validated['description'] ?? null,
            'type'           => $validated['type'],
            'file_path'      => $validated['file_path'] ?? $artifact->file_path,
            'file_name'      => $validated['file_name'] ?? $artifact->file_name,
            'file_size'      => $validated['file_size'] ?? $artifact->file_size,
            'file_mime'      => $validated['file_mime'] ?? $artifact->file_mime,
            'external_url'   => $validated['external_url'] ?? null,
            'thumbnail'      => $thumbnail,
            'category_id'    => $validated['category_id'] ?? null,
            'language'       => $validated['language'] ?? 'en',
            'source'         => $validated['source'] ?? null,
            'published_date' => $validated['published_date'] ?? null,
            'is_featured'    => auth()->user()->isAdmin()
                                    ? $request->boolean('is_featured')
                                    : $artifact->is_featured,
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
        $artifact->tags()->sync($tagIds);

        return redirect()->route('data-room.show', $artifact)
            ->with('success', 'Artifact updated successfully.');
    }

    public function download(Artifact $artifact)
    {
        abort_if(!$artifact->file_path, 404);
        $artifact->incrementDownloads();
        return Storage::disk('public')->download($artifact->file_path, $artifact->file_name);
    }

    public function destroy(Artifact $artifact)
    {
        abort_if(!auth()->user()->isAdmin() && $artifact->user_id !== auth()->id(), 403);

        if ($artifact->file_path) {
            Storage::disk('public')->delete($artifact->file_path);
        }
        $artifact->delete();

        return redirect()->route('data-room.index')->with('success', 'Artifact deleted.');
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' bytes';
    }
}
