<?php

namespace App\Http\Controllers;

use App\Room;

class PageController extends Controller
{

    /**
     * Create a new controller instance
     *
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the home page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function index()
    {

        $user = json_encode(['id' => auth()->user()->id,
                             'name' => auth()->user()->name,
                             'email' => auth()->user()->email,
                             'is_online' => auth()->user()->is_online,
                             'avatar' => auth()->user()->avatar,
                             'registered' => auth()->user()->registered,]);

        $rooms = Room::with('users:name,is_online,avatar')
            ->whereIn('id', auth()->user()->rooms()->get()->pluck('id'))->select('id', 'name')
            ->orderByRaw('(id=1) desc')->orderBy('name', 'asc')->get();

        $initialRooms = auth()->user()->rooms()->orderByRaw('(rooms.id=1) desc')->orderBy('name', 'asc')->get();

        return view('chat', compact('user', 'rooms', 'initialRooms'));
    }
}
