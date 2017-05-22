<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User;
use Illuminate\Http\Request;
use Socialite;

class DashboardController extends Controller
{

    /*
     * Switch cases
     * 1 = Dashboard
     * 2 = Notifications
     * 3 = PayPal Information (Not Implemented)
     */
    public function __construct(Request $request)
    {
        session_start();
    }


    public function index(Request $request)
    {

        $case = 1;
        $user = new  User\UserController();

        $user_dashboard = $user->build($request, $case);

        return response()->json($user_dashboard, 200, [], JSON_PRETTY_PRINT);

    }

    public function messages(Request $request)
    {
        $case = 2;
        $user = new  User\UserController();

        $user_messages = $user->build($request, $case);

        return response()->json($user_messages, 200, [], JSON_PRETTY_PRINT);

    }

    public function earnings(Request $request)
    {
        /* Not implemented yet
        $case = 3;
        $user = new  User\UserController();

        $user_earnings = $user->build($request, $case);

        return response()->json($user_earnings, 200, [], JSON_PRETTY_PRINT);
        */

    }

}