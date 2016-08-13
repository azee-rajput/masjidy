<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$api=app('Dingo\Api\Routing\Router');

Route::get('/', function () {
    return view('welcome');
});


$api->version('v1', function($api){
   $api->get('hello', 'App\Http\Controllers\HomeController@index');
    $api->get('users/{users_id}/roles/{role_name}', 'App\Http\Controllers\HomeController@attachUserRole');
    $api->get('users/{users_id}/roles', 'App\Http\Controllers\HomeController@getUserRole');
    $api->post('role/permission/add', 'App\Http\Controllers\HomeController@attachPermission');
    $api->get('role/{owner}/permission', 'App\Http\Controllers\HomeController@getPermission');
    
    $api->post('user/register', 'App\Http\Controllers\HomeController@register');
    $api->post('user/login', 'App\Http\Controllers\HomeController@login');
    $api->post('authenticate', 'App\Http\Controllers\Auth\AuthController@authenticate');
    
    $api->post('video/upload', 'App\Http\Controllers\FeedController@videoUpload');
    
    $api->get('notification/send', 'App\Http\Controllers\HomeController@testNotification');
    ///////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////
    $api->get('mosque/map/{lng}/{lat}','App\Http\Controllers\HomeController@getMapMosque' );
  //  $api->get('mosque/get/{search}','App\Http\Controllers\AdminController@searchMosque' );
//    $api->get('mosque/add','App\Http\Controllers\AdminController@addMosque' ); 
});

$api->version('v1',['middleware'=>'api.auth'], function($api){
    $api->get('users','App\Http\Controllers\Auth\AuthController@index');
    $api->get('user','App\Http\Controllers\Auth\AuthController@show');
    $api->get('token','App\Http\Controllers\Auth\AuthController@getToken');
    $api->post('delete','App\Http\Controllers\Auth\AuthController@destroy');
    
    $api->post('feed/create', 'App\Http\Controllers\FeedController@createFeed');
    $api->get('feed/get/{offset}', 'App\Http\Controllers\FeedController@showFeed');
    $api->post('feed/delete', 'App\Http\Controllers\FeedController@deleteFeed');
    $api->post('feed/specific', 'App\Http\Controllers\FeedController@specificFeed');
    $api->get('user/followFeed/{offset}/{id}', 'App\Http\Controllers\FeedController@followFeed');
    $api->post('feed/share', 'App\Http\Controllers\FeedController@share');
    
    
    $api->post('feed/like', 'App\Http\Controllers\LikeController@likeUnlike');
    
    $api->get('comment/get/{feedId}', 'App\Http\Controllers\CommentController@showComment');
    $api->post('comment/create', 'App\Http\Controllers\CommentController@createComment');
    $api->post('comment/delete', 'App\Http\Controllers\CommentController@deleteComment');
    
    $api->get('user/search/{search}', 'App\Http\Controllers\HomeController@searchUser');
    $api->get('user/isfollow/{userId}', 'App\Http\Controllers\HomeController@getFollowUnfollow');
    $api->post('user/following', 'App\Http\Controllers\HomeController@followUnfollow');
    $api->post('user/logout', 'App\Http\Controllers\HomeController@logout');
    $api->post('update/profile', 'App\Http\Controllers\HomeController@updateProfile');
    $api->post('update/password', 'App\Http\Controllers\HomeController@updatePassword');
    
    $api->get('notifications/get', 'App\Http\Controllers\NotificationController@getNotifications');
    
    $api->post('notifications/viewed', 'App\Http\Controllers\NotificationController@notificationsViewed');
    
    $api->post('notifications/visited', 'App\Http\Controllers\NotificationController@notificationVisited');
    
    $api->get('notifications/newCount', 'App\Http\Controllers\NotificationController@getNewCount');
    $api->get('feed/getById/{id}', 'App\Http\Controllers\FeedController@getFeedById');
    
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//    $api->get('mosque/get/{lng}/{lat}','App\Http\Controllers\HomeController@getMapMosque' );
    $api->get('mosque/get/{search}','App\Http\Controllers\AdminController@searchMosque' );
    $api->get('mosque/get/all/{search}','App\Http\Controllers\AdminController@getMosque' );
    $api->post('mosque/add','App\Http\Controllers\AdminController@addMosque' );
    $api->get('mosque/manager/mosque/{id}','App\Http\Controllers\AdminController@managerMosque' );
    $api->get('mosque/detail/{id}','App\Http\Controllers\AdminController@mosqueDetail' );
    $api->post('mosque/manager/add','App\Http\Controllers\AdminController@addManager' );
    $api->post('mosque/manager/delete','App\Http\Controllers\AdminController@deleteManager' );
    $api->get('mosque/manager/get','App\Http\Controllers\AdminController@getManager' );
});