<?php

use Illuminate\Database\Seeder;
use App\Country;

class CountriesTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Country::create([
            'name_short_local' => 'Norge',
            'name_short_international' => 'Norway',
            'code_iso' => 'NO',
            'code_call' => '47'
        ]);
    }
}
