<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
 * User routes Auth
 */
Route::middleware('api')->get('auth/instagram/login', 'AuthController@login');
Route::middleware('api')->get('auth/instagram/logout', 'AuthController@logout');
Route::middleware('api')->post('auth/instagram/logout', 'AuthController@logout');
Route::middleware('api')->get('auth/instagram/callback', 'AuthController@callback');

/*
 * User routes Dashboard | Notifications
 */
Route::middleware('api')->get('user/dashboard', 'Dashboard\DashboardController@index');
Route::middleware('api')->get('user/dashboard/notifications', 'Dashboard\DashboardController@messages');


Route::middleware('api')->get('user/dashboard/apply', 'User\UserController@apply_brand');
Route::middleware('api')->get('user/dashboard/view', 'User\UserController@view_ad');
Route::middleware('api')->get('user/dashboard/submit', 'User\UserController@submit_ad');

/*
 * User routes Financial
 */



