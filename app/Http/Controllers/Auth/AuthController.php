<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Socialite;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;


class AuthController extends Controller
{

    public function __construct()
    {

        session_start();

    }

    /**
     * Redirect the user to the Instagram authentication page.
     *
     * @return Response
     */
    public function login()
    {
        return Socialite::driver('instagram')->redirect();
    }

    /**
     * Obtain the user information from Instagram.
     *
     * @return Response
     */
    public function callback()
    {

        $user = Socialite::driver('instagram')->user();

        $token = $user->token;

        session(['token' => $token]);

        return redirect('api/user/dashboard');

    }


}