<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    protected function payout_calculation()
    {

        //convert likes to payout
        //check date on images, use for date range calculations only gather likes between requested dates
        //TODO: Need to check limit on API calls
        
    }

    protected function payout_minimum()
    {

        //$this->payout_calculation();
        $user_money_earnings = '100';
        $minimum = '100';

        if ($user_money_earnings >= $minimum) {

            // pass go :D collect!

        } else {

            // error you don't have minimum payout balance yet

        }
    }

    public function total_earnings(){

        //all_earnings - paid_out earnings = total_earnings
        //for now return total earnings for example need to think more about this shit

        $array = [
            'total_earnings' => '$1050.50'
        ];

        return $array;
    }



}
