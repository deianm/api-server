<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => 'web'], function () {

    /*
     * Brand routes Auth
     */

    Route::post('auth/register', 'AuthController@register_brand');
    Route::post('auth/login', 'AuthController@login_brand');
    Route::post('auth/logout', 'AuthController@logout_brand');

    /*
     * Brand routes Dashboard | Notifications | Actions
     */

    //POST

    Route::post('brand/create_offer', 'Brand\BrandController@create_offer');
    Route::post('brand/cancel_offer', 'Brand\BrandController@cancel_offer');

    Route::post('brand/approve_submission', 'Brand\BrandController@approve_submission');
    Route::post('brand/deny_submission', 'Brand\BrandController@deny_submission');

    Route::post('brand/approve_advertiser', 'Brand\BrandController@approve_advertiser');

    Route::post('brand/request_join', 'Brand\BrandController@request_join');
    Route::post('brand/cancel_join', 'Brand\BrandController@cancel_join');

    //GET

    Route::get('brand/feed', 'Brand\BrandController@brand_feed_json');
    Route::get('brand/offers', 'Brand\BrandController@brand_offers_json');
    Route::get('brand/stats', 'Brand\BrandController@brand_stats_json');
    Route::get('brand/users', 'Brand\BrandController@brand_users_json');
    Route::get('brand/messages', 'Brand\BrandController@brand_msg_json');


    /*
     * Brand routes Financial
     */

});
