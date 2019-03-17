<?php

namespace App;

use Auth;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Traits\UsesUuid;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, UsesUuid, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Checks if the password is correct
     *
     * @param String $password
     * @return boolean
     */
    public function check_password(String $password): bool
    {
        return Auth::attempt(['email' => $this->email, 'password' => $password]);
    }

    public function places()
    {
        return $this->hasManyThrough('App\Place', 'App\PlaceUser', 'user_id', 'id', 'id', 'place_id');
    }
}
