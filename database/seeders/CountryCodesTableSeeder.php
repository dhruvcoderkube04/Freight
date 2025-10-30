<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CountryCode;

class CountryCodesTableSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['code' => 'USA', 'name' => 'United States', 'sort_order' => 1],
            ['code' => 'CAN', 'name' => 'Canada', 'sort_order' => 2],
            ['code' => 'MEX', 'name' => 'Mexico', 'sort_order' => 3],
        ];

        foreach ($countries as $country) {
            CountryCode::create($country);
        }
    }
}