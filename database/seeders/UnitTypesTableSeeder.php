<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UnitType;

class UnitTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $unitTypes = [
            ['code' => 'pallet', 'name' => 'Pallet', 'description' => 'Standard pallet', 'sort_order' => 1],
            ['code' => 'carton', 'name' => 'Carton', 'description' => 'Cardboard carton', 'sort_order' => 2],
            ['code' => 'crate', 'name' => 'Crate', 'description' => 'Wooden crate', 'sort_order' => 3],
            ['code' => 'box', 'name' => 'Box', 'description' => 'Shipping box', 'sort_order' => 4],
            ['code' => 'bundle', 'name' => 'Bundle', 'description' => 'Bundled items', 'sort_order' => 5],
            ['code' => 'drum', 'name' => 'Drum', 'description' => 'Metal or plastic drum', 'sort_order' => 6],
            ['code' => 'roll', 'name' => 'Roll', 'description' => 'Rolled materials', 'sort_order' => 7],
            ['code' => 'case', 'name' => 'Case', 'description' => 'Case of items', 'sort_order' => 8],
            ['code' => 'piece', 'name' => 'Piece', 'description' => 'Individual piece', 'sort_order' => 9],
        ];

        foreach ($unitTypes as $type) {
            UnitType::create($type);
        }
    }
}