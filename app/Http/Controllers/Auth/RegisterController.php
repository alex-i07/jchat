<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public $path;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
//            'avatar' => 'mimes:jpg,jpeg,png,bmp,svg,gif|max:3072',
            'avatar' => 'is_png',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'avatar' => $data['avatar'],
        ]);
    }

    /**
     * Register user
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */

    public function register(Request $request)
    {

        $this->validator($request->all())->validate();

        if (null !== $request->input('avatar'))
        {
            $path = $this->storeFile($request);
        }

        else
        {
            $path = 'avatar-default.png';
        }

//        dd($request->input('avatar'), $path);

//        if (optional($request->file('avatar'))->isValid() !== null)
//        {
//            $path = $this->storeFile($request);
//        }

        $data = array('name' => $request->input('name'), 'email' => $request->input('email'),
                      'password' => $request->input('password'), 'avatar' => $path);

//        $this->traitRegister($request);  //в трейт нужно передать Request $request, а не обычный массив!

        event(new Registered($user = $this->create($data)));

        $this->guard()->login($user);

        return $this->registered($request, $user);
//            ?: redirect($this->redirectPath());

    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        $defaultRoomId = Room::firstOrCreate(['name' => 'All users'])->id;

        $user->rooms()->attach($defaultRoomId);

        LoginController::authenticated($request, $user);

        return response ('Registration successful', 201);

    }

    /**
     * Stores avatar file and returns it name
     *
     * @param Request $request
     * @return string
     */

    protected function storeFile (Request $request)
    {
//        $request->file('avatar')->store('public/users-avatars');
//        return $request->file('avatar')->hashName();

        \Cloudinary::config(array( "cloud_name" => env('CLOUD_NAME'),
                                   "api_key" => env('API_KEY'),
                                   "api_secret" => env('API_SECRET')
        ));

        $response = \Cloudinary\Uploader::upload($request->input('avatar'), array("folder" => "storage/users-avatars/"));

        return $response['secure_url'];
    }
}
