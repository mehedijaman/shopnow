<?php

namespace Modules\Page\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Page\Models\Page;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(4);

        return [
            'title' => $name,
            'slug' => Str::slug($name),
            'content' => $this->faker->paragraphs(5, true),
            'meta_tag_title' => Str::limit($name, 60, ''),
            'meta_tag_description' => Str::limit($this->faker->sentence(10), 160, ''),
            'image' => $this->faker->imageUrl(),

            'published_at' => $this->faker->dateTimeBetween('-6 month', '+3 month'),

            'created_at' => $this->faker->dateTimeBetween('-1 year', '-6 month'),
            'updated_at' => $this->faker->dateTimeBetween('-5 month', 'now'),
        ];
    }
}
