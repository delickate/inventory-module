<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $units = [
            // Weight Units
            ['name' => 'Kilogram', 'abbreviation' => 'kg'],
            ['name' => 'Gram', 'abbreviation' => 'g'],
            ['name' => 'Milligram', 'abbreviation' => 'mg'],
            ['name' => 'Pound', 'abbreviation' => 'lb'],
            ['name' => 'Ounce', 'abbreviation' => 'oz'],
            
            // Volume Units
            ['name' => 'Liter', 'abbreviation' => 'L'],
            ['name' => 'Milliliter', 'abbreviation' => 'mL'],
            ['name' => 'Gallon', 'abbreviation' => 'gal'],
            ['name' => 'Quart', 'abbreviation' => 'qt'],
            ['name' => 'Pint', 'abbreviation' => 'pt'],
            
            // Length/Distance Units
            ['name' => 'Meter', 'abbreviation' => 'm'],
            ['name' => 'Centimeter', 'abbreviation' => 'cm'],
            ['name' => 'Millimeter', 'abbreviation' => 'mm'],
            ['name' => 'Inch', 'abbreviation' => 'in'],
            ['name' => 'Foot', 'abbreviation' => 'ft'],
            
            // Counting Units
            ['name' => 'Piece', 'abbreviation' => 'pc'],
            ['name' => 'Dozen', 'abbreviation' => 'dz'],
            ['name' => 'Pack', 'abbreviation' => 'pk'],
            ['name' => 'Box', 'abbreviation' => 'bx'],
            ['name' => 'Carton', 'abbreviation' => 'ctn'],
            
            // Area Units
            ['name' => 'Square Meter', 'abbreviation' => 'm²'],
            ['name' => 'Square Foot', 'abbreviation' => 'ft²'],
            
            // Other Common Inventory Units
            ['name' => 'Roll', 'abbreviation' => 'rl'],
            ['name' => 'Set', 'abbreviation' => 'set'],
            ['name' => 'Pair', 'abbreviation' => 'pr'],
            ['name' => 'Bundle', 'abbreviation' => 'bdl'],
            ['name' => 'Each', 'abbreviation' => 'ea'],
        ];

        // Insert units
        DB::table('units')->insert($units);
    }
}
