<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'All electronic devices and components'
            ],
            [
                'name' => 'Clothing',
                'description' => 'Apparel and fashion items'
            ],
            [
                'name' => 'Furniture',
                'description' => 'Home and office furniture'
            ],
            [
                'name' => 'Groceries',
                'description' => 'Food and household consumables'
            ],
            [
                'name' => 'Stationery',
                'description' => 'Office and school supplies'
            ],
            [
                'name' => 'Tools',
                'description' => 'Hardware and equipment'
            ],
            [
                'name' => 'Toys',
                'description' => 'Children\'s toys and games'
            ],
            [
                'name' => 'Sports Equipment',
                'description' => 'Fitness and sporting goods'
            ],
            [
                'name' => 'Books',
                'description' => 'Educational and recreational reading materials'
            ],
            [
                'name' => 'Health & Beauty',
                'description' => 'Personal care and wellness products'
            ],
            [
                'name' => 'Automotive',
                'description' => 'Vehicle parts and accessories'
            ],
            [
                'name' => 'Jewelry',
                'description' => 'Fine jewelry and watches'
            ],
            [
                'name' => 'Home Appliances',
                'description' => 'Large and small household appliances'
            ],
            [
                'name' => 'Construction Materials',
                'description' => 'Building and construction supplies'
            ],
            [
                'name' => 'Miscellaneous',
                'description' => 'Items that don\'t fit other categories'
            ]
        ];

        // Insert categories
        DB::table('categories')->insert($categories);
    }
}
