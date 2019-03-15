<?php

namespace App;

use DateTime;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PlaceOpenHour extends Model
{
    use UsesUuid;

    public $timestamps = false;

    public function getTimeFromAttribute($value)
    {
        if (DateTime::createFromFormat('H:i:s', $value) === false) {
            return null;
        }

        return Carbon::createFromFormat('H:i:s', $value)->format('H:i');
    }

    public function getTimeToAttribute($value)
    {
        if (DateTime::createFromFormat('H:i:s', $value) === false) {
            return null;
        }

        return Carbon::createFromFormat('H:i:s', $value)->format('H:i');
    }

    // Following ISO 8601 for weekday numbering
    public static function getWeekdays()
    {
        return [
            1 => __('Monday'),
            2 => __('Tuesday'),
            3 => __('Wednesday'),
            4 => __('Thursday'),
            5 => __('Friday'),
            6 => __('Saturday'),
            7 => __('Sunday')
        ];
    }

    public static function getWeekdayName()
    {
        return self::getWeekdays()[Carbon::now()->dayOfWeekIso];
    }

    public static function getWeekdayNumber(String $weekday)
    {
        return array_search(ucfirst($weekday), self::getWeekdays());
    }

    public static function validateTimes($time_from_input, $time_to_input)
    {
        $error = false;

        $time_from = DateTime::createFromFormat('H:i', $time_from_input);
        $time_to = DateTime::createFromFormat('H:i', $time_to_input);

        $time_from_is_invalid = $time_from === false;
        $time_to_is_invalid = $time_to === false;

        if ($time_from_input == null) {
            $error = true;
            $error_message = 'The opening time on :weekday is missing.';
        } else if ($time_to_input == null) {
            $error = true;
            $error_message = 'The closing time on :weekday is missing.';
        } else if ($time_from_is_invalid) {
            $error = true;
            $error_message = 'The opening time on :weekday is not on the correct format.';
        } else if ($time_to_is_invalid) {
            $error = true;
            $error_message = 'The closing time on :weekday is not on the correct format.';
        } else if ($time_from > $time_to) {
            $error = true;
            $error_message = 'The opening time on :weekday comes after the closing time.';
        }

        if ($error) {
            return $error_message;
        }

        return true;
    }
}
