<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use JWTAuth;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'time', 'title', 'description', 'admin_id', 'venue', 'price'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'admin_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment', 'item_id');
    }

    public function likes()
    {
        return $this->morphToMany('App\User', 'likeable')->whereDeletedAt(null);
    }

    public function getIsLikedAttribute()
    {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['msg' => 'User not authenticated'], 404);
        }
        $like = $this->likes()->whereUserId($user->id)->first();
        return (!is_null($like)) ? true : false;
    }
}