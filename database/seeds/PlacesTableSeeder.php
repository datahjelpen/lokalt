<?php

use Illuminate\Database\Seeder;
use App\Place;
use App\PlaceRole;
use App\PlaceType;
use App\PlaceOpenHour;

class PlacesTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $place_type_business = PlaceType::create([
            'name' => 'Bedrift',
            'slug' => 'business',
            'description' => 'En bedrift/selskap. F.eks. et AS eller ENK.'
        ]);

        factory(Place::class, 50)->create();
        factory(PlaceOpenHour::class, 50)->create();

        PlaceRole::create([
            'name' => 'Eier',
            'slug' => 'owner',
            'place_type_id' => $place_type_business->id,
            'description' => 'Eier/daglig leder av bedriften.'
        ]);

        PlaceRole::create([
            'name' => 'Avdelingsleder',
            'slug' => 'department_manager',
            'place_type_id' => $place_type_business->id,
            'description' => 'Ansvarlig for en avdeling innenfor bedriften.'
        ]);
    }
}
