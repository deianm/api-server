<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Notification;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Instagram;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Payment;
use Illuminate\Http\Request;


//use Illuminate\Http\Request;


class UserController extends Controller
{

    /*
     *  instagram_id is the unique identifier not using user_id... why? just because.
     *  Responses example displayed above methods
     */

    public $instagram;
    public $payment;
    public $message;

    public function __construct()
    {

        $this->instagram = New Instagram\InstagramController();
        $this->payment = New Payment\PaymentController();
        $this->message = New Notification\NotificationController();

    }

    /*
     * Register a user
     */
    public function register($user_information)
    {

        $instagram_id = $user_information->data->id;
        $followers = $user_information->data->counts->follows;
        $user_name = $user_information->data->username;
        $full_name = $user_information->data->full_name;
        $profile_picture = $user_information->data->profile_picture;

        DB::table('advertising_users')->insert([
            'instagram_id' => $instagram_id,
            'followers' => $followers,
            'user_name' => $user_name,
            'full_name' => $full_name,
            'profile_picture' => $profile_picture
        ]);

    }

    /* Instagram API response
     *
        "user_profile": {
            "profile_picture": "https:\/\/scontent.cdninstagram.com\/t51.2885-19\/s150x150\/16789420_1269933329741848_2756110419700482048_a.jpg",
            "website": "",
            "id": "4687339110",
            "username": "dmy_dm",
            "full_name": "Dmitri Yuri",
            "bio": "",
            "counts": {
                "follows": 0,
                "followed_by": 0,
                "media": 2
            }
        },
     *
     */

    public function user_profile()
    {
        $token = $_SESSION['token'];
        $result = $this->instagram->instagramUser($token);
        return $result->data;

    }


    /*
     *
     *
     *
     *
     * Portions of final User Dashboard response --SECTION--
     *
     *
     *
     *
     *
     * 1 = new
     * 2 = approved
     * 3 = pending
     * 4 = denied
     * 0 = leaving 0 alone for now...
     *
     * Need to determine what a "new_brand" refers to for each user
     */

    public function retrieve_status($status, $instagram_id)
    {

        switch ($status) {
            case 1: //new
                $result = DB::table('brand_status')
                    ->where('status', '=', $status)
                    ->where('instagram_id', '=', $instagram_id)
                    ->get();
                return $result;
                break;
            case 2: //approved
                $result = DB::table('brand_status')
                    ->where('status', '=', $status)
                    ->where('instagram_id', '=', $instagram_id)
                    ->get();
                return $result;
                break;
            case 3: //pending
                $result = DB::table('brand_status')
                    ->where('status', '=', $status)
                    ->where('instagram_id', '=', $instagram_id)
                    ->get();
                return $result;
                break;
            case 4; //denied
                $result = DB::table('brand_status')
                    ->where('status', '=', $status)
                    ->where('instagram_id', '=', $instagram_id)
                    ->get();
                return $result;
                break;

        }

        //Got tired of my IDE yelling at me...
        return [];

    }

    /*
     *
      "new_brands": {
            "brand_name": {
                "RaceTrack": {
                    "id": "1",
                    "time_created": "12-12-12 12:12:12",
                },
                "AlienWare": {
                    "id": "2",
                    "time_created": "12-12-12 12:12:12",
                }
            }
        },
     *
     */

    public function new_brands($instagram_id)
    {

        $status = 1; // New Brands

        $new_brand = '';

        $result = $this->retrieve_status($status, $instagram_id);

        //dd($result);
        //die;

        foreach ($result as $brand) {

            $brand_id = $brand->id;
            $result_db = DB::table('users')
                ->where('id', '=', $brand_id)
                ->get();
            $brand_name = $result_db[0]->brand_name;
            $brand_ca = $brand->created_at;

            $new_brand[] = [
                $brand_name => [
                    'details' => [
                        'id' => $brand_id,
                        'time_created' => $brand_ca,
                    ]
                ]
            ];

        }

        return $new_brand;

    }

    /*
     *
       "approved_brands": [
            {
                "brand_name": {
                    "not_created_yet": {
                        "id": 3,
                        "time_created": "2017-03-14 17:46:24"
                    }
                }
            }
        ],
     *
     */

    public function approved_brands($instagram_id)
    {

        $status = 2; // New Brands

        $approved_brand = '';

        $result = $this->retrieve_status($status, $instagram_id);

        foreach ($result as $brand) {

            $brand_id = $brand->id;
            $result_db = DB::table('users')
                ->where('id', '=', $brand_id)
                ->get();
            //dd($result_db);
            $brand_name = $result_db[0]->brand_name;
            $brand_ca = $brand->created_at;

            $approved_brand[] = [
                $brand_name => [
                    'details' => [
                        'id' => $brand_id,
                        'time_created' => $brand_ca,
                    ]
                ]
            ];

        }

        return $approved_brand;

    }

    /*
     *
       "pending_brands": [
            {
                "brand_name": {
                    "not_created_yet": {
                        "id": 5,
                        "time_created": "2017-03-14 17:46:28"
                    }
                }
            }
        ],
     *
     */

    public function pending_brands($instagram_id)
    {

        $status = 3; // New Brands

        $pending_brand = '';

        $result = $this->retrieve_status($status, $instagram_id);

        foreach ($result as $brand) {

            $brand_id = $brand->id;
            $result_db = DB::table('users')
                ->where('id', '=', $brand_id)
                ->get();
            //dd($result_db);
            $brand_name = $result_db[0]->brand_name;
            $brand_ca = $brand->created_at;

            $pending_brand[] = [
                $brand_name => [
                    'details' => [
                        'id' => $brand_id,
                        'time_created' => $brand_ca,
                    ]
                ]
            ];

        }

        return $pending_brand;

    }


    /*
     *
        "denied_brands": [
            {
                "brand_name": {
                    "not_created_yet": {
                        "id": 8,
                        "time_created": "2017-03-14 17:46:33"
                    }
                }
            }
        ],
     *
     */

    public function denied_brands($instagram_id)
    {

        $status = 4; // New Brands

        $denied_brand = '';

        $result = $this->retrieve_status($status, $instagram_id);

        foreach ($result as $brand) {

            $brand_id = $brand->id;
            $result_db = DB::table('users')
                ->where('id', '=', $brand_id)
                ->get();
            $brand_name = $result_db[0]->brand_name;
            $brand_ca = $brand->created_at;

            $denied_brand[] = [
                $brand_name => [
                    'details' => [
                        'id' => $brand_id,
                        'time_created' => $brand_ca,
                    ]
                ]
            ];

        }

        return $denied_brand;

    }

    /*
     *
     *
     *
     *
     *
     *
     * User Actions --SECTION--
     *
     *
     *
     *
     *
     */

    public function apply_brand(Request $request)
    {

        //Only brand_id is required to be passed
        session_start();

        $status = 3; //Pending

        //$instagram_id = $_SESSION['instagram_id'];
        //$brand_id = $request->brand_id;

        $instagram_id = '4687339110';
        $brand_id = rand(3,42);

        DB::table('brand_status')->insert([
            'instagram_id' => $instagram_id,
            'brand_id' => $brand_id,
            'status' => $status
        ]);

    }

    public function view_ad(Request $request){

        session_start();

        $brand_id = $request->brand_id;
        $hashtag =  'randopenpaper'; //$request->hashtag;
        $image_details = [];

        $instagram_id = '4687339110';
        $token = $_SESSION['token'];
        $results = $this->instagram->instagramSelfMedia($instagram_id, $token);

        $user_images = $results->data;

        foreach ($user_images as $key => $image ) {

            $tag_array = $image->tags;

            $tag_exists = in_array($hashtag, $tag_array);

            if($tag_exists == true) {

                $image_details[] = $image;

            }

        }

        return response()->json($image_details, 200, [], JSON_PRETTY_PRINT);

    }

    public function submit_ad($brand_id, $image_id)
    {
        $instagram_id = '4687339110';
        $brand_id = '3';
        $image_id = '';
        //target brand
        //take user_id and image_id and add it to brand
        //Blah blah here

    }

    /*
     *
     *
     *
     *
     *
     * Complied array for Json response --SECTION--
     *
     * - Dashboard  function build();
     * - Notifications  function notifications();
     * - PayPal Information function paypal();
     * - More to come... I am sure
     *
     */

    public function build(Request $request, $response)
    {
        //TODO: need to figure out if to use user_id from db or just use instagram_id and say fuck it for now..

            $instagram_id = $_SESSION['instagram_id'];

            //$instagram_id = '4687339110'; Test id user dmy_dm pass Bobino22392239! can use this account however

            switch ($response) {
                case 1:

                    //Basic information fo user
                    //from DB
                    $new_brands = $this->new_brands($instagram_id);
                    $approved_brands = $this->approved_brands($instagram_id);
                    $pending_brands = $this->pending_brands($instagram_id);
                    //$denied_brands = $this->denied_brands($instagram_id);

                    //from Instagram api
                    $user_profile = $this->user_profile();

                    //Financial Information
                    //from DB
                    $user_earnings = $this->payment->total_earnings();
                    $user_msg_count = $this->message->user_message_count($instagram_id);

                    $array = [
                        'success' => 'true',
                        'data' => [
                            'user_profile' =>
                                $user_profile
                            ,
                            'offers' =>
                                $new_brands
                            ,
                            'brands' =>
                                $approved_brands
                            ,
                            'history' =>
                                $pending_brands
                            ,
                            /* Taking this out for now...
                            'denied_brands' =>
                                $denied_brands
                            ,
                            */
                            'earnings' =>
                                $user_earnings
                            ,
                            'notifications' =>
                                $user_msg_count

                        ]
                    ];

                    return $array;
                    break;

                case 2:

                    $user_notifications = $this->message->user_messages($instagram_id);

                    $array = [
                        'success' => 'true',
                        'data' => [
                            'user_notifications' =>
                                $user_notifications,
                        ]
                    ];
                    return $array;
                    break;
            }

            return [];

    }

}
