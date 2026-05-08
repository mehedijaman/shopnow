<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Page\Models\Page;

use function Laravel\Prompts\info;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        info('Seeding system pages...');

        $systemPages = [
            [
                'title' => 'About Us',
                'slug' => 'about',
                'content' => '<p>Write your About Us content here.</p>',
                'meta_tag_title' => 'About Us',
                'meta_tag_description' => 'Learn more about who we are, our mission, and what drives us.',
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<p>Write your Privacy Policy content here.</p>',
                'meta_tag_title' => 'Privacy Policy',
                'meta_tag_description' => 'Read our privacy policy to understand how we handle your data.',
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content' => '<p>Write your Terms of Service content here.</p>',
                'meta_tag_title' => 'Terms of Service',
                'meta_tag_description' => 'Review the terms and conditions for using our platform.',
            ],
            [
                'title' => 'Refund Policy',
                'slug' => 'refund-policy',
                'content' => '<p>Write your Refund Policy content here.</p>',
                'meta_tag_title' => 'Refund Policy',
                'meta_tag_description' => 'Read our refund and return policy.',
            ],
        ];

        foreach ($systemPages as $data) {
            Page::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'is_system' => true,
                    'active' => true,
                    'published_at' => now(),
                ])
            );
        }

        info('System pages seeded.');
    }
}
