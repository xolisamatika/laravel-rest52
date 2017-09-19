<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id', 'item_type', 'from_user', 'body'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'from_user');
    }

    public function event()
    {
        return $this->belongsTo('App\Event', 'item_id');
    }

    public function likes()
    {
        return $this->morphToMany('App\User', 'likeable')->whereDeletedAt(null);
    }
}
