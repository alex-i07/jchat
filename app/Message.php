<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['user_id', 'room_id', 'body', 'arrived_at'];

    /**
     *
     *
     * @var bool
     */

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}