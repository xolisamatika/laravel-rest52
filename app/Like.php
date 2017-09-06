<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Like extends Model
{
    use SoftDeletes;

    protected $table = 'likeables';

    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
    ];

    /**
     * Get all of the events that are assigned this like.
     */
    public function events()
    {
        return $this->morphedByMany('App\Event', 'likeable');
    }

    /**
     * Get all of the comments that are assigned this like.
     */
    public function comments()
    {
        return $this->morphedByMany('App\Comment', 'likeable');
    }
}
