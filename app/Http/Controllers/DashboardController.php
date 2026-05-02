<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use App\Models\ForumThread;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $recentArtifacts = Artifact::with(['user', 'category'])
            ->where('is_published', true)
            ->latest()
            ->take(6)
            ->get();

        $featuredArtifacts = Artifact::with(['user', 'category'])
            ->where('is_published', true)
            ->where('is_featured', true)
            ->latest()
            ->take(3)
            ->get();

        $recentThreads = ForumThread::with(['user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'artifacts' => Artifact::where('is_published', true)->count(),
            'threads' => ForumThread::count(),
            'users' => User::count(),
        ];

        return view('dashboard', compact('recentArtifacts', 'featuredArtifacts', 'recentThreads', 'stats'));
    }
}
