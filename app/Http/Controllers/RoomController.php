<?php

namespace App\Http\Controllers;

use App\User;
use App\Room;
use App\Events\UserLeftRoom;
use App\Events\NewRoomCreated;
use Illuminate\Http\Request;

class RoomController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Create a new room
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|\Symfony\Component\HttpFoundation\Response|static|static[]
     */

    public static function addRoom(Request $request)
    {

        if ( !empty($request->input('name')) && !empty($request->input('users'))) {

            $users = User::whereIn('name', $request->input('users'))->whereRaw(' `is_online` <> "Offline"')->get();

            if ($users->isNotEmpty()) {

                $room = Room::create(['name' => request('name')]);

                $room->users()->attach(auth()->user());
                $room->users()->attach($users);

                $room = Room::with('users:name,is_online,avatar')->select('id', 'name')->find($room->id);

                broadcast(new NewRoomCreated($room))->toOthers();

                return $room;
            } else {
                return response("Don't know how to name this error, will name it later", 418);
            }
        }

        return response('Empty rooms are not allowed!', 400);

    }

    /**
     * Delete user from selected room
     *
     * @param $roomId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */

    public function closeRoom($roomId)
    {

        if ($roomId == 1) {
            return response('Your can\'t delete yourself from the first room!', 400);
        }

        $room = Room::find($roomId);

        $room->users()->detach(auth()->user());

        /*if current user is only user in this room then no need to
        send info through web sockets
        */

        if ($room->users()->get()->isNotEmpty()) {
            broadcast(new UserLeftRoom($room, auth()->user()->name))->toOthers();
        }

        return response('Your was successfully deleted from this room', 200);

    }
}
