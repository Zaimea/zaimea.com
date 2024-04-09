<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Zaimea\Database\Seeders\ZaimeaSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            ZaimeaSeeder::runSeeder()
        );
    }
}
