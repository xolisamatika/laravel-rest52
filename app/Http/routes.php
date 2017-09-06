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
Route::get('/', function () {
    return view('welcome');
});


// Route::auth();
Route::get('/home', 'FrontController@index');
Route::get('/events', 'FrontController@events');

Route::group(['prefix' => 'api/v1','middleware' => 'cors'], function () {
    Route::resource('event', 'EventController', [
        'except' => ['edit', 'create']
    ]);

    Route::resource('event/registration', 'RegistrationController', [
        'only' => ['store', 'destroy']
    ]);

    Route::post('user', ['as' => 'user.save', 'uses' => 'AuthController@store'
    ]);

    Route::post('user/signin', ['as' => 'user.signin', 'uses' => 'AuthController@signin'
    ]);

    Route::get('event/like/{id}', ['as' => 'event.like', 'uses' => 'LikeController@likeEvent'
    ]);

    Route::get('comment/like/{id}', ['as' => 'comment.like', 'uses' => 'LikeController@likeComment'
    ]);
});
