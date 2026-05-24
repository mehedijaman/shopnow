<?php

namespace Modules\Slider\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Slider\Models\Slider;

use function Laravel\Prompts\info;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        info('Creating Sliders...');
        Slider::factory()->count(10)->create();
        info('Sliders created.');

        Schema::enableForeignKeyConstraints();
    }
}
