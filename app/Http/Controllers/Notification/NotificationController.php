<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class NotificationController extends Controller
{

    public function __construct()
    {


    }


    /*
     *
     * message_type
     * 1 = approved
     * 2 = denied
     * 3 = submitted application
     * 4 = submitted image
     *
     * message_status
     * 1 = read
     * 2 = unread
     *
     *
     */

    public function user_message_count($instagram_id)
    {
        $result = DB::table('notifications')->where('instagram_id', '=', $instagram_id)->get();
        $read_count = '';
        $unread_count = '';
        $read_i = 0;
        $unread_i = 1; //Some fucking bug is count this shit as -1 bleh.... fuck it made it 1;

        //dd($result);

        foreach ($result as $msg_count) {

            $read_count = $read_i;
            $read = 1;
            $message_status = $msg_count->message_status;

            if ($message_status == $read) {
                $read_i++;
            }

        }

        foreach ($result as $msg_count) {

            $unread_count = $unread_i;
            $unread = 2;
            $message_status = $msg_count->message_status;

            if ($message_status == $unread) {
                $unread_i++;
            }

        }

        $array = [
            'total_count' => count($result),
            'read_count' => $read_count,
            'unread_count' => $unread_count
        ];

        return $array;

    }

    public function user_messages($instagram_id)
    {
        $result = DB::table('notifications')->where('instagram_id', '=', $instagram_id)->get();
        $messages = '';

        foreach ($result as $msg) {

            $message = $msg->message;
            $message_type = $msg->message_type;
            $message_status = $msg->message_status;

            $messages[] = [
                'message' => $message,
                'message_type' => $message_type,
                'message_status' => $message_status
            ];

        }

        return $messages;

    }

    public function approved_msg()
    {


    }

    public function denied_msg()
    {


    }

    public function submit_msg()
    {


    }

    public function submit_image_msg()
    {


    }

}
