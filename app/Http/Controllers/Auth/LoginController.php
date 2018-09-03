<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers{
        logout as protected traitLogout;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Logout user
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function logout(Request $request)
    {

        $userId = auth()->user()->id;

        $this->changeOnlineTime($userId);

        return $this->traitLogout($request);
//        return redirect($this->redirectTo);
//        return $this->redirectPath();

    }

    /**
     * Change is_online column value of specified user to 'Offline'
     *
     * @param $userId
     */

    protected function changeOnlineTime($userId)
    {

        $user = User::find($userId);

        $user->is_online = 'Offline';

        $user->save();

    }

    /**
     * Write time when the user has been authenticated.
     *
     * @param Request $request
     * @param User $user
     */

    public static function authenticated(Request $request = null, User $user)
    {
        $user->is_online = Carbon::now()->timezone('Atlantic/Azores')->toAtomString();
        $user->save();

    }
}
