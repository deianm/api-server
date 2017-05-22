<?php

namespace App\Http\Controllers\Instagram;

/*
 * OCD kicked in...
 */
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\User;
use Illuminate\Http\Request;
use App\Http\Controllers;
use GuzzleHttp\Client;
use Socialite;

class InstagramController extends Controller
{

    public function __construct()
    {

    }

    //First time login -> register user id to db
    //$user_instagram_id = $user_information->data->id;

    public function userCheck($user_information)
    {
        if (isset($user_information->data->id) == false) {

            return redirect('api/auth/instagram/login');

        } else {

            $user_instagram_id = $user_information->data->id;

            $user = DB::table('advertising_users')->where('instagram_id', $user_instagram_id)->count();

            if (empty($user)) {

                $register = new  User\UserController();

                $register->register($user_information);

                $_SESSION['instagram_id'] = $user_information->data->id;

            } else {

                $_SESSION['instagram_id'] = $user_information->data->id;

            }
        }

        //Got tired of my IDE yelling at me...
        return [];

    }


    /*
     * Retrieve basic user information
     */
    public function instagramUser($token)
    {
        $url = 'https://api.instagram.com/v1/users/self/?access_token=' . $token;
        $response = $this->send_request($url);
        return $response;

    }

    /*
     * Retrieve single media record based on media_id
     */
    public function instagramSingleMedia($media_id, $token)
    {

        $url = 'https://api.instagram.com/v1/media/' . $media_id . '?access_token=' . $token;
        $response = $this->send_request($url);
        return $response;

    }

    /*
     * Retrieve recent media for self
     */
    public function instagramSelfMedia($instagram_id, $token)
    {

        $url = 'https://api.instagram.com/v1/users/' . $instagram_id . '/media/recent/?access_token=' . $token;
        $response = $this->send_request($url);
        return $response;

    }


    public function send_request($url)
    {

        $client = new Client();

        try {
            $response = $client->get($url, [
                'connect_timeout' => 10
            ])->getBody();

            return json_decode($response);

        } catch (RequestException $e) {

            if ($e->getResponse()->getStatusCode() == '400') {

                return redirect('api/auth/login');
            }

        } catch (\Exception $e) {

            //Other Exceptions

        }

        //Got tired of my IDE yelling at me...
        return [];

    }

}