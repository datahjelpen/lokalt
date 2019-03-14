<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use UsesUuid;

    public $timestamps = false;
}
