<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use function Laravel\Prompts\info;
use Illuminate\Filesystem\Filesystem;
use Modules\Customer\Models\Customer;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Sequence;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        info('Creating customers...');
        Customer::factory()->count(10)->create();
        info('Customers created.');

        Schema::enableForeignKeyConstraints();
    }
}
