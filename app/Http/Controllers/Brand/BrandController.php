<?php

namespace App\Http\Controllers\Brand;

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


//use Illuminate\Http\Request;


class BrandController extends Controller
{

    /*
     *  Check if user is logged in, if so authorize and build $user variable
     *  Responses example displayed above methods
     */

    public $user;

    public function __construct(Request $request)
    {

        $this->user = Sentinel::getUser();

    }

    public function retrieve_brand($status, $brand_id)
    {

        switch ($status) {
            case 1: //feed
                $result = DB::table('user_brands')
                    ->where('status', '=', $status)
                    ->where('instagram_id', '=', $brand_id)
                    ->get();
                return $result;
                break;
            //BREAK//

            case 2: //offers
                $current_offers_array = [];
                $i = 0;

                $current_offers = DB::table('brand_offers')
                    ->where('brand_id', '=', $brand_id)
                    ->where('expired', '=', '1')
                    ->get();

                foreach ($current_offers as $offer){

                    $start_date = date_create($offer->start_date);
                    $expiration_date = date_create($offer->expiration_date);

                    $days_count = date_diff($start_date, $expiration_date);
                    $current_offers_array[$i]['id'] = $offer->id;
                    $current_offers_array[$i]['brand_id'] = $offer->brand_id;
                    $current_offers_array[$i]['hashtag'] = $offer->hashtag;
                    $current_offers_array[$i]['offer_details'] = $offer->offer_details;
                    $current_offers_array[$i]['days_left'] = $days_count->days;
                    $current_offers_array[$i]['offer_cost'] = $offer->offer_cost;
                    $current_offers_array[$i]['offer_posts'] = $offer->offer_posts;
                    $current_offers_array[$i]['offer_likes'] = $offer->offer_likes;

                    $i++;

                }

                $current_offers_object = (object) $current_offers_array;

                $expired_offers = DB::table('brand_offers')
                    ->where('brand_id', '=', $brand_id)
                    ->where('expired', '=', '0')
                    ->get();

                $data = [
                    'current_offers' => $current_offers_object,
                    'expired_offers' => $expired_offers
                ];

                return $data;
                break;
            //BREAK//

            case 3: //stats
                $result = DB::table('user_brands')
                    ->where('status', '=', $status)
                    ->where('instagram_id', '=', $brand_id)
                    ->get();
                return $result;
                break;
            //BREAK//

            case 4: //users


                $new_users = DB::table('advertising_users')->get();

                $pending_users = DB::table('brand_status')->get();
                $new_users_result = [];

                for ($i = 0, $c = count($new_users); $i < $c; ++$i) {
                    $new_users[$i] = (array) $new_users[$i];
                }

                $i = 0;

                foreach($pending_users as $pending_user) {

                    $pending_instagram_id = $pending_user->instagram_id;
                    $pending_brand_id = $pending_user->brand_id;
                    $pending_status = $pending_user->status;


                    //var_dump($new_users_array);

                    if(in_array($pending_instagram_id, $new_users[$i]) && ($pending_brand_id == $brand_id)) {

                        $new_users_result = $new_users;


                    } else {

                        echo 'please work';
                    }

                    $i++;
                }


                //Display Affiliated Users Only
                $brand_users = DB::table('assigned_users')->where('assigned_brand_id', '=', $brand_id)->get();

                $data = [
                    'new_users' => $new_users_result,
                    'pending_users' => $pending_users,
                    'brand_users' => $brand_users
                ];

                return $data;
                break;
            //BREAK//

            case 5; //messages
                $message = [];
                $i = 0;

                $results = DB::table('brand_notifications')
                    ->where('brand_id', '=', $brand_id)
                    ->get();

                foreach ($results as $result)
                {
                    $message[$i]['message'] = $result->message;
                    $message[$i]['message_type'] = $result->message_type;
                    $message[$i]['message_status'] = $result->message_status;
                    $i++;
                }

                $data = $message;

                return $data;
                break;
            //BREAK//

        }

        //Got tired of my IDE yelling at me...
        return [];

    }

    public function brand_feed($brand_id)
    {

        $status = 1; // New Brands

        $new_brand = '';

        $result = $this->retrieve_status($status, $brand_id);

        //dd($result);
        //die;

        foreach ($result as $brand) {

            $brand_id = $brand->id;
            $brand_name = 'not_created_yet';
            $brand_ca = $brand->created_at;

            $new_brand[] = [
                'brand_name' => [
                    $brand_name => [
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
     * BRAND ACTIONS
     *
     * -create_offer - POST
     * -cancel_offer - POST
     *
     * -approve_submission - POST
     * -decline_submission - POST
     *
     * -approve_advertiser - POST
     * -decline_advertiser - POST
     *
     * -request_join - POST
     * -revoke_join - POST
     */

    public function create_offer(Request $request)
    {

        //One is for the web application the other is for phone app "brand_id"

        $brand_id = $request->brand_id; //After Sentinel check user user variable to get the ID of the logged in Brand

        if(empty($brand_id)) {
            $brand_id = $this->user->id;
        }

        $hashtag = $request->hashtag; //Mention hashtag to use
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $offer_details = $request->offer_details;
        $new_offer = '1';

        //Default values
        $post_count = 0;
        $offer_cost = 0;
        $offer_likes = 0;

        DB::table('brand_offers')->insert([
            'brand_id' => $brand_id,
            'hashtag' => $hashtag,
            'start_date' => $start_date,
            'expiration_date' => $end_date,
            'expired' => $new_offer,
            'offer_details' => $offer_details,
            'offer_likes' => $offer_likes,
            'offer_posts' => $post_count,
            'offer_cost' => $offer_cost
        ]);

        $response = [
            'data' => [
                'success' => 'true',
                'details' => 'Sent successfully!',
            ]
        ];

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);

    }

    public function cancel_offer(Request $request)
    {

        $status = 0; //Cancel

        $id = $request->id;

        DB::table('brand_offers')
            ->where('id', '=', $id)
            ->update(['expired' => $status
            ]);

        $response = [
            'data' => [
                'success' => 'true',
                'details' => 'Sent successfully!',
            ]
        ];

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);

    }

    public function approve_submission(Request $request)
    {
        //Approves submission by user
    }

    public function decline_submission(Request $request)
    {
        //Denies submission by user
    }

    public function approve_advertiser(Request $request)
    {
        /*
        $status = 2;

        //$instagram_id = $request->instagram_id;
        $brand_id = $this->user->id;
        $instagram_id = '4687339110';

        DB::table('brand_status')
            ->where('instagram_id', '=', $instagram_id)
            ->where('brand_id', '=', $brand_id)
            ->update(['status' => $status
            ]);
        */

    }

    public function deny_advertiser(Request $request)
    {

        /*
        $status = 4;

        //$instagram_id = $request->instagram_id;
        $brand_id = $this->user->id;
        $instagram_id = '4687339110';

        DB::table('brand_status')
            ->where('instagram_id', '=', $instagram_id)
            ->where('brand_id', '=', $brand_id)
            ->update(['status' => $status
            ]);
        */

    }

    public function request_join(Request $request)
    {

        $status = 2; //Pending

        $instagram_id = $request->instagram_id;
        $brand_id = $request->brand_id;

        DB::table('brand_status')->insert([
            'instagram_id' => $instagram_id,
            'brand_id' => $brand_id,
            'status' => $status
        ]);

        $response = [
            'data' => [
                'success' => 'true',
                'details' => 'Sent successfully!',
            ]
        ];

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);

    }

    public function revoke_join(Request $request)
    {

        $status = 4; //Cancel

        $status_id = $request->id;
        $brand_id = $request->brand_id;

        DB::table('brand_status')
            ->where('id', '=', $status_id)
            ->where('brand_id', '=', $brand_id)
            ->update(['status' => $status
        ]);

        $response = [
            'data' => [
                'success' => 'true',
                'details' => 'Sent successfully!',
            ]
        ];

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);

    }


    /*
     *
     * JSON RESPONSES
     * - FEED - GET
     * - OFFERS - GET
     * - STATS - GET
     * - USERS - GET
     * - MESSAGES - GET
     *
     */

    public function brand_feed_json(){

        $brand_id = $this->user->id;
        $status = 1;

        $feeds =  $this->retrieve_brand($status, $brand_id);

        $response = [
            'data' => [
                'success' => 'true',
                'feed' => $feeds
            ]
        ];

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }

    public function brand_offers_json(){

        $brand_id = $this->user->id;
        $status = 2;

        $offers =  $this->retrieve_brand($status, $brand_id);

        $response = [
            'data' => [
                'success' => 'true',
                'offers' => $offers,
            ]
        ];

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);

    }

    public function brand_stats_json(){

        $brand_id = $this->user->id;
        $status = 3;

        $stats =  $this->retrieve_brand($status, $brand_id);

        $response = [
            'data' => [
                'success' => 'true',
                'feed' => $stats
            ]
        ];

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }

    public function brand_users_json(){

        $brand_id = $this->user->id;
        $status = 4;

        $users =  $this->retrieve_brand($status, $brand_id);

        $response = [
            'data' => [
                'success' => 'true',
                'users' => $users
            ]
        ];

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }

    public function brand_msg_json(){

        $brand_id = $this->user->id;
        $status = 5;

        $messages = $this->retrieve_brand($status, $brand_id);

        $response = [
            'data' => [
                'success' => 'true',
                'messages' => $messages,
            ]
        ];

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);

    }

}
