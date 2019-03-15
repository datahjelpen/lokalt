<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use UsesUuid;

    public $timestamps = false;

    public static function findByInfo(String $street_name_number, String $postal_code, String $postal_city, String $province)
    {
        return Address::where([
            'street_name_number' => $street_name_number,
            'postal_code' => $postal_code,
            'postal_city' => $postal_city,
            'province' => $province
        ])->first();
    }
}
