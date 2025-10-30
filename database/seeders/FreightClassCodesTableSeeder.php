<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FreightClassCode;

class FreightClassCodesTableSeeder extends Seeder
{
    public function run(): void
    {
        $freightClasses = [
            ['code' => '50', 'description' => 'Dense Freight', 'sort_order' => 1],
            ['code' => '55', 'description' => 'Dense Freight', 'sort_order' => 2],
            ['code' => '60', 'description' => 'Dense Freight', 'sort_order' => 3],
            ['code' => '65', 'description' => 'Average Freight', 'sort_order' => 4],
            ['code' => '70', 'description' => 'Average Freight', 'sort_order' => 5],
            ['code' => '77.5', 'description' => 'Average Freight', 'sort_order' => 6],
            ['code' => '85', 'description' => 'Average Freight', 'sort_order' => 7],
            ['code' => '92.5', 'description' => 'Less Dense Freight', 'sort_order' => 8],
            ['code' => '100', 'description' => 'Less Dense Freight', 'sort_order' => 9],
            ['code' => '110', 'description' => 'Less Dense Freight', 'sort_order' => 10],
            ['code' => '125', 'description' => 'Light Freight', 'sort_order' => 11],
            ['code' => '150', 'description' => 'Light Freight', 'sort_order' => 12],
            ['code' => '175', 'description' => 'Light Freight', 'sort_order' => 13],
            ['code' => '200', 'description' => 'Light Freight', 'sort_order' => 14],
            ['code' => '250', 'description' => 'Very Light Freight', 'sort_order' => 15],
            ['code' => '300', 'description' => 'Very Light Freight', 'sort_order' => 16],
            ['code' => '400', 'description' => 'Very Light Freight', 'sort_order' => 17],
            ['code' => '500', 'description' => 'Very Light Freight', 'sort_order' => 18],
        ];

        foreach ($freightClasses as $class) {
            FreightClassCode::create($class);
        }
    }
}