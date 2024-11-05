<?php

namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Customer\Models\Customer;

use function Laravel\Prompts\info;

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
