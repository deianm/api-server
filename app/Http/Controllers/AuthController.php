<?php

namespace App\Http\Controllers;

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Cartalyst\Sentinel\Native\SentinelBootstrapper;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Socialite;



class AuthController extends Controller
{

    public function __construct()
    {

        session_start();

    }


    /*
     *
     *
     *
     * User Section --------------
     *
     *
     *
     */

    /**
     * Redirect the user to the Instagram authentication page.
     *
     * @return Response
     */
    public function login()
    {
        return Socialite::driver('instagram')->redirect();
    }


    public function logout(){

        session_destroy();
        $_SESSION['logged_in'] = false;
        return redirect('api/auth/instagram/login');

    }

    /**
     * Obtain the user information from Instagram.
     *
     * @return Response
     */
    public function callback(Request $request)
    {

        $user = Socialite::driver('instagram')->user();
        $instagram = new Instagram\InstagramController();

        $token = $user->token;

        session(['token' => $token]);

        $_SESSION['token'] = $token = $request->session()->get('token');

        $user_information = $instagram->instagramUser($token);

        $instagram->userCheck($user_information);

        if(isset($_SESSION['instagram_id']) == true){

            $_SESSION['logged_in'] = true;
            return redirect('api/user/dashboard');

        } else {

            echo 'Bye bye!';

        }

    }

    /*
     *
     *
     *
     * Brand Section --------------
     *
     *
     *
     */

    public function register_brand(Request $request){

        $email = $request->email;
        $password = $request->password;
        $brand_name = $request->brand_name;

        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        $user = Sentinel::registerAndActivate($credentials);

        $brand_id = $user->id;

        DB::table('users')
            ->where('id', $brand_id)
            ->update(['brand_name' => $brand_name]);

        $response = [
            'success' => 'true'
        ];

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);


    }


    public function login_brand(Request $request) {

        $email = $request->email;
        $password = $request->password;

        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        $auth = Sentinel::authenticateAndRemember($credentials);

        if (!empty($auth)){

            $response = [
                'data' => [
                    'success' => 'true',
                    'auth' => $auth
                ]
            ];

            return response()->json($response, 200, [], JSON_PRETTY_PRINT);

        } else {

            $response = [
                'data' => [
                    'success' => 'false',
                    'auth' => 'Try again.'
                ]
            ];

            return response()->json($response, 422, [], JSON_PRETTY_PRINT);

        }

    }


    public function logout_brand() {

        Sentinel::logout();

    }


}