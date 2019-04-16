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

    public $appends = [
        "weekday_name",
        "opening_hours",
        "opening_hours_no_ex",
        "date"
    ];

    public function getTimeFromCarbonAttribute()
    {
        if ($this->time_from === null) {
            return null;
        }

        return Carbon::createFromFormat('H:i', $this->time_from);
    }

    public function getTimeFromAttribute($value)
    {
        $datetime_format = DateTime::createFromFormat('H:i:s', $value);
        if ($datetime_format === false) {
            return null;
        }

        return $datetime_format->format('H:i');
    }

    public function getTimeToCarbonAttribute()
    {
        if ($this->time_to === null) {
            return null;
        }

        return Carbon::createFromFormat('H:i', $this->time_to);
    }

    public function getTimeToAttribute($value)
    {
        $datetime_format = DateTime::createFromFormat('H:i:s', $value);
        if ($datetime_format === false) {
            return null;
        }

        return $datetime_format->format('H:i');
    }

    public function getSpecialHoursDateAttribute($value)
    {
        $datetime_format = DateTime::createFromFormat('Y-m-d', $value);
        if ($datetime_format === false) {
            return null;
        }

        return $datetime_format->format('Y-m-d');
    }

    public function getDateAttribute()
    {
        $datetime_format = DateTime::createFromFormat('Y-m-d', $this->special_hours_date);
        if ($datetime_format === false) {
            return null;
        }

        return strftime("%d. %B", $datetime_format->getTimestamp());
    }


    public function timeFromTo(bool $show_normal = true)
    {
        if ($this->special_hours_date !== null && $this->time_from == null && $show_normal && $this->normal_time_from != null) {
            return __('Closed') . '. (' . __('Normally:') . ' ' . $this->normal_time_from . '-' . $this->normal_time_to . ')';
        }

        if ($this->time_from == null) {
            return __('Closed');
        }

        return $this->time_from . '-' . $this->time_to;
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

    public static function getWeekdayName(int $weekday)
    {
        return self::getWeekdays()[$weekday];
    }

    public static function getWeekdayNumber(String $weekday)
    {
        return array_search(ucfirst($weekday), self::getWeekdays());
    }

    public function getWeekDayNameAttribute()
    {
        if ($this->weekday == null) {
            return null;
        }

        return self::getWeekdayName($this->weekday);
    }

    public function getOpeningHoursAttribute()
    {
        return $this->timeFromTo();
    }

    public function getOpeningHoursNoExAttribute()
    {
        return $this->timeFromTo(false);
    }

    public static function getAvailableHours() : array
    {
        $available_hours = [];

        // Generate from 06:00-00:00
        for ($hour=6; $hour < 24; $hour++) {
            $hour_string = $hour;

            if ($hour < 10) {
                $hour_string = '0' . $hour;
            }

            // Add every 10th minute
            for ($minute=0; $minute < 6; $minute++) {
                $minute_string = $minute*10;

                if ($minute*10 < 10) {
                    $minute_string = '0' . $minute*10;
                }

                array_push($available_hours, $hour_string .':'. $minute_string);
            }
        }

        // Generate from 00:00-06:00
        for ($hour=0; $hour < 6; $hour++) {
            $hour_string = $hour;

            if ($hour < 10) {
                $hour_string = '0' . $hour;
            }

            // Add every 10th minute
            for ($minute=0; $minute < 6; $minute++) {
                $minute_string = $minute*10;

                if ($minute*10 < 10) {
                    $minute_string = '0' . $minute*10;
                }

                array_push($available_hours, $hour_string .':'. $minute_string);
            }
        }


        return $available_hours;
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
        }

        if ($error) {
            return $error_message;
        }

        return true;
    }
}
