<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // ── Get or create a user to seed posts for ────
        $user = User::where('id', 4)->first();

        if (! $user) {
            $user = User::factory()->create([
                'name'     => 'Demo User',
                'email'    => 'demo@example.com',
                'password' => bcrypt('password'),
            ]);
            $this->command->info("Created demo user: demo@example.com / password");
        }

        $this->command->info("Seeding 100 posts for: {$user->name} ({$user->email})");

        // ── Clear existing posts for this user ─────────
        Post::where('user_id', $user->id)->delete();
        $this->command->info("Cleared existing posts.");

        // ── Seed 60 published posts ────────────────────
        Post::factory()
            ->count(1000)
            ->published()
            ->create(['user_id' => $user->id]);

        $this->command->info("✅ Seeded 60 published posts.");

        // ── Seed 40 draft posts ────────────────────────
        Post::factory()
            ->count(3000)
            ->draft()
            ->create(['user_id' => $user->id]);

        $this->command->info("✅ Seeded 40 draft posts.");

        $this->command->info("🎉 Done! 100 posts seeded (60 published, 40 drafts).");
    }
}