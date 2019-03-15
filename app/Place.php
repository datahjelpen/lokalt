<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\UsesUuid;
use App\User;
use App\PlaceUser;
use App\PlaceOpenHour;

class Place extends Model
{
    use UsesUuid;

    public static function findBySlug(String $slug)
    {
        return self::where('slug', $slug)->first();
    }

    public function users()
    {
        return $this->belongsToMany('App\Place')->withPivot('user_id', 'place_id');
    }

    public function address()
    {
        return $this->belongsTo('App\Address');
    }

    public function place_type()
    {
        return $this->belongsTo('App\PlaceType');
    }

    public function userHasAccess(User $user)
    {
        $place_user = PlaceUser::where([
            'user_id' => $user->id,
            'place_id' => $this->id
        ])->first();

        return ($place_user !== null);
    }

    public function getOpeningHoursRegularAttribute()
    {
        return PlaceOpenHour::where('place_id', $this->id)->whereIn('weekday', [1, 2, 3, 4, 5, 6, 7])->get();
    }
}
