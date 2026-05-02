<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataRoom\ArtifactController;
use App\Http\Controllers\Forum\ForumController;
use App\Http\Controllers\Forum\ThreadController;
use App\Http\Controllers\Forum\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

// Landing → redirect to dashboard if logged in, else show welcome
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Data Room
Route::prefix('data-room')->name('data-room.')->group(function () {
    Route::get('/', [ArtifactController::class, 'index'])->middleware('auth')->name('index');
    Route::get('/upload', [ArtifactController::class, 'create'])->middleware('auth')->name('create');
    Route::post('/upload', [ArtifactController::class, 'store'])->middleware('auth')->name('store');
    Route::get('/{artifact:slug}', [ArtifactController::class, 'show'])->middleware('auth')->name('show');
    Route::get('/{artifact:slug}/edit', [ArtifactController::class, 'edit'])->middleware('auth')->name('edit');
    Route::put('/{artifact:slug}', [ArtifactController::class, 'update'])->middleware('auth')->name('update');
    Route::get('/{artifact:slug}/download', [ArtifactController::class, 'download'])->middleware('auth')->name('download');
    Route::delete('/{artifact:slug}', [ArtifactController::class, 'destroy'])->middleware('auth')->name('destroy');

    // Comments on artifacts
    Route::post('/{artifact:slug}/comments', [CommentController::class, 'store'])->middleware('auth')->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->middleware('auth')->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->middleware('auth')->name('comments.destroy');
});

// Forum
Route::prefix('forum')->name('forum.')->middleware('auth')->group(function () {
    Route::get('/', [ForumController::class, 'index'])->name('index');
    Route::post('/', [ForumController::class, 'storeCategory'])->name('category.store');
    Route::put('/category/{category}', [ForumController::class, 'updateCategory'])->name('category.update');
    Route::get('/threads/{thread}/edit', [ThreadController::class, 'edit'])->name('thread.edit');
    Route::put('/threads/{thread}', [ThreadController::class, 'update'])->name('thread.update');
    Route::delete('/threads/{thread}', [ThreadController::class, 'destroy'])->name('thread.destroy');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('post.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('post.destroy');
    Route::get('/{category:slug}', [ForumController::class, 'category'])->name('category');
    Route::get('/{category:slug}/new', [ThreadController::class, 'create'])->name('thread.create');
    Route::post('/{category:slug}/new', [ThreadController::class, 'store'])->name('thread.store');
    Route::get('/{category:slug}/{thread:slug}', [ThreadController::class, 'show'])->name('thread');
    Route::post('/{category:slug}/{thread:slug}/reply', [PostController::class, 'store'])->name('post.store');
});

// Admin
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.role');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');
    Route::get('/tags', [AdminController::class, 'tags'])->name('tags');
    Route::post('/tags', [AdminController::class, 'storeTag'])->name('tags.store');
    Route::delete('/tags/{tag}', [AdminController::class, 'destroyTag'])->name('tags.destroy');
});

require __DIR__.'/auth.php';
