<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('messages-channel.{id}', function ($user, $id) {

    if (auth()->user()->rooms()->get()->pluck('id')->contains($id))
    {
        return $user;
    }

});

Broadcast::channel('user.{id}', function ($user, $id){

    return (int) $user->id === (int) $id;

});