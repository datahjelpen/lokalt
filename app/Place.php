<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use App\User;
use App\PlaceUser;
use App\PlaceOpenHour;
use App\Traits\UsesUuid;

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

    public function getOpeningHoursAttribute()
    {
        return $this->openingHours();
    }

    public function openingHours(bool $limit = true)
    {
        // Get all regular and special hours
        $open_hours_regular = PlaceOpenHour::where('place_id', $this->id)->
                                             whereIn('weekday', [1, 2, 3, 4, 5, 6, 7])->
                                             orderBy('weekday')->
                                             get();

        if ($limit) {
            $open_hours_special =  PlaceOpenHour::where('place_id', $this->id)->
                                                  whereDate('special_hours_date', '>=', Carbon::now())->
                                                  whereDate('special_hours_date', '<', Carbon::now()->addMonths(2))->
                                                  orderBy('special_hours_date')->
                                                  get();
        } else {
            $open_hours_special =  PlaceOpenHour::where('place_id', $this->id)->
                                                  whereDate('special_hours_date', '>=', Carbon::now())->
                                                  orderBy('special_hours_date')->
                                                  get();
        }

        $open_hours = new \stdClass;
        $open_hours->regular = [];
        $open_hours->special = [];

        for ($i=1; $i <= 7 ; $i++) {
            $regular = new PlaceOpenHour;
            $open_hours->regular[$i] = $regular;
        }

        // Add regular hours to array, indexed by weekday
        foreach ($open_hours_regular as $regular) {
            $open_hours->regular[$regular->weekday] = $regular;
        }

        $monday    = Carbon::now()->startOfWeek();
        $sunday    = Carbon::now()->endOfWeek();

        // Add special hours to array
        foreach ($open_hours_special as $special) {
            $special_date = new Carbon($special->special_hours_date);
            $special->weekday = $special_date->dayOfWeekIso;

            array_push($open_hours->special, $special);

            if ($special_date->isBetween($monday, $sunday, true)) {
                $regular = $open_hours->regular[$special->weekday];

                $special->normal_time_from = $regular->time_from;
                $special->normal_time_to = $regular->time_to;

                $open_hours->regular[$special->weekday] = $special;
            }
        }

        return $open_hours;
    }
}
