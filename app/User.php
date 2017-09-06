<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function events()
    {
        return $this->hasMany('App\Event', 'admin_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment', 'from_user');
    }

    public function likedEvent()
    {
        return $this->morphedByMany('App\Event', 'likeable')->whereDeletedAt(null);
    }

    public function likedComment()
    {
        return $this->morphedByMany('App\Comment', 'likeable')->whereDeletedAt(null);
    }
}
