<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    // ── Title templates ──────────────────────────────
    private array $titleTemplates = [
        'How to :verb Your :noun in :number Easy Steps',
        'The Ultimate Guide to :noun',
        'Why :noun Matters More Than You Think',
        ':number Things Every :person Should Know About :noun',
        'The Truth About :noun Nobody Talks About',
        'How I :verb My :noun and Changed Everything',
        'A Beginner\'s Guide to :noun',
        'The Best :noun Strategies for :year',
        'Why I Stopped :verb and Started :verb',
        'The Secret to :verb Like a Pro',
        'What Nobody Tells You About :noun',
        'How to :verb :noun Without Losing Your Mind',
        'The :number Most Common :noun Mistakes',
        'Everything You Need to Know About :noun',
        ':number Reasons Why :noun is the Future',
    ];

    private array $verbs = [
        'Master', 'Improve', 'Build', 'Launch', 'Scale',
        'Optimize', 'Transform', 'Automate', 'Simplify', 'Grow',
        'Design', 'Develop', 'Create', 'Fix', 'Manage',
    ];

    private array $nouns = [
        'Laravel API', 'React Native App', 'Mobile UI', 'Backend System',
        'Database Schema', 'Authentication Flow', 'REST API', 'Dark Mode',
        'Pagination', 'Search Feature', 'User Profile', 'Dashboard',
        'Deployment Pipeline', 'Code Architecture', 'State Management',
        'Performance', 'Security', 'Testing Strategy', 'Tech Stack',
        'Side Project',
    ];

    private array $persons = [
        'Developer', 'Engineer', 'Designer', 'Founder',
        'Freelancer', 'Team Lead', 'Junior Dev', 'Full-Stack Dev',
    ];

    // ── Generate Random Title ─────────────────────────
    private function generateTitle(): string
    {
        $template = $this->faker->randomElement($this->titleTemplates);

        return str_replace(
            [':verb', ':noun', ':number', ':person', ':year'],
            [
                $this->faker->randomElement($this->verbs),
                $this->faker->randomElement($this->nouns),
                $this->faker->randomElement([3, 5, 7, 10, 12, 15]),
                $this->faker->randomElement($this->persons),
                $this->faker->randomElement([2024, 2025, 2026]),
            ],
            $template
        );
    }

    // ── Generate Rich Body ────────────────────────────
    private function generateBody(): string
    {
        $paragraphs = $this->faker->numberBetween(3, 6);
        $body       = [];

        $openers = [
            "In this post, we'll explore one of the most important topics in modern development.",
            "If you've been struggling with this topic, you're not alone.",
            "After years of building projects, I've learned some valuable lessons worth sharing.",
            "Let's dive into what makes this topic so important and how you can apply it today.",
            "This is something I wish someone had told me when I was starting out.",
        ];

        $body[] = $this->faker->randomElement($openers);

        for ($i = 0; $i < $paragraphs; $i++) {
            $body[] = $this->faker->paragraph($this->faker->numberBetween(4, 8));
        }

        $closers = [
            "I hope this post helped you understand the topic better. Feel free to share your thoughts in the comments.",
            "That's a wrap! Let me know if you have any questions or feedback.",
            "Thanks for reading. If you found this useful, consider sharing it with your team.",
            "Happy coding! Remember — consistency is key when learning something new.",
        ];

        $body[] = $this->faker->randomElement($closers);

        return implode("\n\n", $body);
    }

    // ── Factory Definition ────────────────────────────
  public function definition(): array
{
    // ✅ Random date between Jan 1 and Mar 31, 2026
    $createdAt = $this->faker->dateTimeBetween('2026-01-01', '2026-03-31');

    return [
        'user_id'    => User::factory(),
        'title'      => $this->generateTitle(),
        'body'       => $this->generateBody(),
        'status'     => $this->faker->randomElement(['draft', 'published']),
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ];
}

    // ── States ────────────────────────────────────────
public function published(): static
{
    $createdAt = $this->faker->dateTimeBetween('2026-01-01', '2026-03-31');
    return $this->state([
        'status'     => 'published',
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ]);
}

public function draft(): static
{
    $createdAt = $this->faker->dateTimeBetween('2026-01-01', '2026-03-31');
    return $this->state([
        'status'     => 'draft',
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ]);
}
}