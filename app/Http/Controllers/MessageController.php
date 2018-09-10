<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\Room;
use App\Message;
use Illuminate\Support\Facades\Auth;
use App\CustomPagination\PrettyPaginator;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use App\Events\IncomingMessage;
use Illuminate\Http\Request;

class MessageController extends Controller
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
     * Post a new message
     *
     * @param Request $request
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|null|\Symfony\Component\HttpFoundation\Response
     */

    public function messageAdd(Request $request)
    {

        $this->validate(request(), [
            'roomName' => 'required|string',
            'body' => 'required'
        ]);

        $allowedRoom = auth()->user()->rooms->pluck('id', 'name');

        $roomName = $request->input('roomName');

        $roomId = $allowedRoom->get($roomName);

        $userId = auth()->user()->id;

        $body = $request->input('body');

        $name = auth()->user()->name;

        $avatar = auth()->user()->avatar;

        $is_online = auth()->user()->is_online;

        $arrived_at = Carbon::now()->timezone('Atlantic/Azores')->format('Y-m-d\TH:i:s.uP');

        if ($roomId)
        {
            Message::create([
                'user_id' => $userId,
                'room_id' => $roomId,
                'body'    => $body,
                'arrived_at' => $arrived_at,
            ]);

            $messageToSend = ['body' => $body, 'arrived_at' => $arrived_at, 'user' =>
                ['name' => $name, 'avatar' => $avatar, 'is_online' => $is_online]];

            return event(
                (new IncomingMessage($messageToSend, $roomId))->dontBroadcastToCurrentUser()
            );

        }

        return response('You are not allowed to write to this room', 403);

    }

    /**
     * Get messages for a given room and page
     *
     * @param $roomId
     * @param $page
     * @return $this|PrettyPaginator instance
     */

    public function fetchRoomMessages($roomId, $page)
    {

        $allowedRoom = auth()->user()->rooms->pluck('name', 'id')->get($roomId);

        if (!$allowedRoom)
        {
            return response('Your are not allowed to read messages from this room!', 403);
        }

        $perPage = env('PER_PAGE');

        $total = Message::where('room_id', '=', $roomId)->count();

        if($page > ceil($total/$perPage))
        {
            return response ('', 204);    //204 No Content
        }

        $roomMessagesUser = Message::with('user:id,name,avatar,is_online')->select('body', 'arrived_at',
            'user_id', 'room_id')->where('room_id', '=', $roomId)->latest('arrived_at')->skip(($page-1)*$perPage)
            ->take($perPage)->get()->transform(function($value) {return collect($value)->except(['room_id', 'user_id', 'user.id']);});

        $roomMessagesUser = new PrettyPaginator ($roomMessagesUser, $total, $perPage, $page, $roomId);

        return response($roomMessagesUser, 200);

    }
}
