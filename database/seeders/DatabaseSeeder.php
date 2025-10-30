<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            FreightClassCodesTableSeeder::class,
            UnitTypesTableSeeder::class,
            CountryCodesTableSeeder::class,
            LocationTypesTableSeeder::class,
        ]);
    }
}