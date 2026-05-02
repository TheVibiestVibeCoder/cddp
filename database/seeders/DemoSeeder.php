<?php

namespace Database\Seeders;

use App\Models\Artifact;
use App\Models\Category;
use App\Models\Comment;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumThread;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * DEMO DATA SEEDER
 * All records created here are clearly prefixed with [TEST] or [DEMO].
 * To remove all demo data: php artisan db:seed --class=DemoSeeder (with a wipe flag)
 * Or simply run: php artisan migrate:fresh to start clean.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ─── USERS ───────────────────────────────────────────────────────────
        $admin = User::create([
            'name'         => '[TEST] Admin User',
            'email'        => 'admin@demo.test',
            'password'     => Hash::make('password'),
            'role'         => 'admin',
            'organization' => 'CDDP Test Organisation',
            'bio'          => 'TEST ACCOUNT - Platform administrator for demo purposes.',
        ]);

        $researcher = User::create([
            'name'         => '[TEST] Researcher',
            'email'        => 'researcher@demo.test',
            'password'     => Hash::make('password'),
            'role'         => 'user',
            'organization' => 'TEST Research Institute',
            'bio'          => 'TEST ACCOUNT - Regular user with posting rights.',
        ]);

        $readonly = User::create([
            'name'         => '[TEST] Read-Only User',
            'email'        => 'readonly@demo.test',
            'password'     => Hash::make('password'),
            'role'         => 'readonly',
            'organization' => 'TEST Observer',
            'bio'          => 'TEST ACCOUNT - Read-only access.',
        ]);

        // ─── TAGS ─────────────────────────────────────────────────────────────
        $tags = collect([
            'TEST-propaganda', 'TEST-social-media', 'TEST-russia', 'TEST-election',
            'TEST-deepfake', 'TEST-fact-check', 'TEST-analysis',
        ])->map(fn($name) => Tag::create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]));

        // ─── DATA ROOM CATEGORIES ─────────────────────────────────────────────
        $catReports = Category::create([
            'name'        => '[TEST] Reports',
            'slug'        => 'test-reports',
            'description' => 'TEST CATEGORY - Research reports and analyses.',
            'icon'        => '📊',
        ]);
        $catVideos = Category::create([
            'name'        => '[TEST] Videos',
            'slug'        => 'test-videos',
            'description' => 'TEST CATEGORY - Video evidence and recordings.',
            'icon'        => '🎥',
        ]);
        $catBriefs = Category::create([
            'name'        => '[TEST] Briefings',
            'slug'        => 'test-briefings',
            'description' => 'TEST CATEGORY - Policy briefs and summaries.',
            'icon'        => '📋',
            'parent_id'   => $catReports->id,
        ]);

        // ─── ARTIFACTS ────────────────────────────────────────────────────────
        $artifact1 = Artifact::create([
            'title'       => '[TEST] TEST DOCUMENT — Sample Disinformation Report',
            'slug'        => 'test-document-sample-report-' . Str::random(4),
            'summary'     => 'TEST SUMMARY: This is a demo document entry for testing purposes only.',
            'description' => "TEST DESCRIPTION\n\nThis artifact was created by the DemoSeeder. It does not contain real content.\n\nTo remove all demo data, run: php artisan migrate:fresh --seed or delete records prefixed with [TEST].",
            'type'        => 'report',
            'user_id'     => $admin->id,
            'category_id' => $catReports->id,
            'is_featured' => true,
            'is_published'=> true,
            'source'      => 'TEST SOURCE',
            'language'    => 'en',
        ]);

        $artifact2 = Artifact::create([
            'title'       => '[TEST] TEST BRIEF — Sample Policy Brief',
            'slug'        => 'test-brief-policy-' . Str::random(4),
            'summary'     => 'TEST SUMMARY: Sample policy brief for demo.',
            'description' => 'TEST DESCRIPTION: Demo brief created by DemoSeeder.',
            'type'        => 'brief',
            'user_id'     => $researcher->id,
            'category_id' => $catBriefs->id,
            'is_published'=> true,
            'language'    => 'en',
        ]);

        $artifact3 = Artifact::create([
            'title'       => '[TEST] TEST LINK — Sample External Resource',
            'slug'        => 'test-link-external-' . Str::random(4),
            'summary'     => 'TEST SUMMARY: Link to an external resource (demo).',
            'type'        => 'link',
            'external_url'=> 'https://example.com',
            'user_id'     => $researcher->id,
            'is_published'=> true,
            'language'    => 'en',
        ]);

        // Attach tags
        $artifact1->tags()->attach($tags->take(3)->pluck('id'));
        $artifact2->tags()->attach($tags->skip(2)->take(2)->pluck('id'));

        // ─── COMMENTS ON ARTIFACTS ────────────────────────────────────────────
        $comment = Comment::create([
            'body'             => '[TEST COMMENT] This is a demo comment added by the seeder. Safe to delete.',
            'user_id'          => $researcher->id,
            'commentable_type' => Artifact::class,
            'commentable_id'   => $artifact1->id,
        ]);
        Comment::create([
            'body'             => '[TEST REPLY] This is a demo reply to the test comment.',
            'user_id'          => $admin->id,
            'commentable_type' => Artifact::class,
            'commentable_id'   => $artifact1->id,
            'parent_id'        => $comment->id,
        ]);

        // ─── FORUM CATEGORIES ─────────────────────────────────────────────────
        $forumGeneral = ForumCategory::create([
            'name'        => '[TEST] General Discussion',
            'slug'        => 'test-general-discussion',
            'description' => 'TEST FORUM CATEGORY: General test discussion board.',
            'icon'        => '💬',
            'color'       => '#111827',
            'order'       => 1,
        ]);
        $forumAnalysis = ForumCategory::create([
            'name'        => '[TEST] Analysis & Methods',
            'slug'        => 'test-analysis-methods',
            'description' => 'TEST FORUM CATEGORY: Methodology and analysis discussion.',
            'icon'        => '🔬',
            'color'       => '#1e3a5f',
            'order'       => 2,
        ]);

        // ─── FORUM THREADS ────────────────────────────────────────────────────
        $thread1 = ForumThread::create([
            'title'             => '[TEST] TEST THREAD — Welcome to the Demo Forum',
            'slug'              => 'test-thread-welcome-' . Str::random(4),
            'body'              => "[TEST CONTENT]\n\nThis is a demo thread created by the DemoSeeder.\n\nAll content prefixed with [TEST] can be safely removed. To wipe all demo data, run:\n\n  php artisan migrate:fresh\n\nThis will reset the database completely.",
            'user_id'           => $admin->id,
            'forum_category_id' => $forumGeneral->id,
            'is_pinned'         => true,
            'last_reply_at'     => now(),
            'last_reply_user_id'=> $admin->id,
        ]);

        $thread2 = ForumThread::create([
            'title'             => '[TEST] TEST THREAD — Sample Analysis Discussion',
            'slug'              => 'test-thread-analysis-' . Str::random(4),
            'body'              => '[TEST CONTENT] Demo thread for testing the analysis category.',
            'user_id'           => $researcher->id,
            'forum_category_id' => $forumAnalysis->id,
            'last_reply_at'     => now()->subHour(),
            'last_reply_user_id'=> $researcher->id,
        ]);

        // ─── FORUM POSTS (replies) ────────────────────────────────────────────
        ForumPost::create([
            'body'            => '[TEST REPLY] This is a demo reply to the welcome thread.',
            'user_id'         => $researcher->id,
            'forum_thread_id' => $thread1->id,
        ]);
        ForumPost::create([
            'body'            => '[TEST REPLY] Another demo reply for testing the forum display.',
            'user_id'         => $readonly->id,
            'forum_thread_id' => $thread1->id,
        ]);

        // Update denormalised counts
        $thread1->update(['replies_count' => 2]);
        $thread2->update(['replies_count' => 0]);
        $forumGeneral->update(['threads_count' => 1, 'posts_count' => 2]);
        $forumAnalysis->update(['threads_count' => 1, 'posts_count' => 0]);

        // Attach tags to threads
        $thread1->tags()->attach($tags->first()->id);

        $this->command->info('✓ Demo data seeded. Credentials:');
        $this->command->info('  Admin:     admin@demo.test / password');
        $this->command->info('  User:      researcher@demo.test / password');
        $this->command->info('  Read-only: readonly@demo.test / password');
        $this->command->info('All demo records are prefixed with [TEST] for easy identification.');
    }
}
