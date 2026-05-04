<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\ForumThread;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    private function requireAdmin(): void
    {
        abort_if(!auth()->user()?->isAdmin(), 403);
    }

    public function index()
    {
        $this->requireAdmin();

        $stats = [
            'users'     => User::count(),
            'artifacts' => Artifact::count(),
            'threads'   => ForumThread::count(),
            'admins'    => User::where('role', 'admin')->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentArtifacts = Artifact::with('user')->latest()->take(5)->get();

        return view('admin.index', compact('stats', 'recentUsers', 'recentArtifacts'));
    }

    public function users(Request $request)
    {
        $this->requireAdmin();

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'role' => 'required|in:admin,user,readonly',
        ]);

        // Prevent removing the last admin
        if ($user->role === 'admin' && $validated['role'] !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            abort_if($adminCount <= 1, 422, 'Cannot remove the last admin account.');
        }

        // Prevent changing your own role (avoid accidental self-lockout)
        abort_if($user->id === auth()->id() && $validated['role'] !== 'admin', 422, 'You cannot change your own role.');

        $user->forceFill(['role' => $validated['role']])->save();
        return back()->with('success', "User role updated to {$validated['role']}.");
    }

    public function destroyUser(User $user)
    {
        $this->requireAdmin();
        abort_if($user->id === auth()->id(), 403, 'You cannot delete yourself.');
        $user->delete();
        return back()->with('success', 'User deleted.');
    }

    public function forcePasswordReset(User $user)
    {
        $this->requireAdmin();
        abort_if($user->id === auth()->id(), 403, 'Use the normal password change for your own account.');

        Password::sendResetLink(['email' => $user->email]);

        return back()->with('success', "Password reset email sent to {$user->email}. Their current sessions will expire on next request.");
    }

    public function categories()
    {
        $this->requireAdmin();
        $categories = Category::with('children')->whereNull('parent_id')->orderBy('order')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'description'  => 'nullable|string',
            'parent_id'    => 'nullable|exists:categories,id',
            'icon'         => 'nullable|string|max:50',
            'cover_image'  => 'nullable|image|max:5120',
        ]);

        $coverImage = null;
        if ($request->hasFile('cover_image')) {
            Storage::disk('public')->makeDirectory('category-covers');
            $coverImage = $request->file('cover_image')->store('category-covers', 'public') ?: null;
        }

        Category::create([
            'name'        => $validated['name'],
            'slug'        => \Illuminate\Support\Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'parent_id'   => $validated['parent_id'] ?? null,
            'icon'        => $validated['icon'] ?? null,
            'cover_image' => $coverImage,
        ]);

        return back()->with('success', 'Category created.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'icon'        => 'nullable|string|max:50',
            'cover_image' => 'nullable|image|max:5120',
        ]);

        $coverImage = $category->cover_image;
        if ($request->hasFile('cover_image')) {
            if ($coverImage && !str_starts_with($coverImage, 'http')) {
                Storage::disk('public')->delete($coverImage);
            }
            Storage::disk('public')->makeDirectory('category-covers');
            $coverImage = $request->file('cover_image')->store('category-covers', 'public') ?: $category->cover_image;
        }

        $category->update([
            'name'        => $validated['name'],
            'slug'        => \Illuminate\Support\Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'parent_id'   => $validated['parent_id'] ?? null,
            'icon'        => $validated['icon'] ?? null,
            'cover_image' => $coverImage,
        ]);

        return back()->with('success', 'Category updated.');
    }

    public function destroyCategory(Category $category)
    {
        $this->requireAdmin();
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }

    public function tags()
    {
        $this->requireAdmin();
        $tags = Tag::withCount(['artifacts', 'threads'])->orderBy('name')->get();
        return view('admin.tags', compact('tags'));
    }

    public function storeTag(Request $request)
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'name'  => 'required|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        Tag::create([
            'name'  => $validated['name'],
            'slug'  => \Illuminate\Support\Str::slug($validated['name']),
            'color' => $validated['color'] ?? '#374151',
        ]);

        return back()->with('success', 'Tag created.');
    }

    public function destroyTag(Tag $tag)
    {
        $this->requireAdmin();
        $tag->delete();
        return back()->with('success', 'Tag deleted.');
    }
}
