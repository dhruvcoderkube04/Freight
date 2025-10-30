<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LocationType;

class LocationTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $locationTypes = [
            ['code' => 'commercial', 'name' => 'Commercial', 'description' => 'Business or commercial location', 'sort_order' => 1],
            ['code' => 'residential', 'name' => 'Residential', 'description' => 'Residential home address', 'sort_order' => 2],
            ['code' => 'limited_access', 'name' => 'Limited Access', 'description' => 'Location with restricted access', 'sort_order' => 3],
            ['code' => 'trade_show', 'name' => 'Trade Show', 'description' => 'Trade show or convention center', 'sort_order' => 4],
        ];

        foreach ($locationTypes as $type) {
            LocationType::create($type);
        }
    }
}