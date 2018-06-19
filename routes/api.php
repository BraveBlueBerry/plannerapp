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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/tasks', ['as' => 'indexTasks', 'uses' => 'TaskController@index']);
Route::get('/task/{id}', ['as' => 'showTask', 'uses' => 'TaskController@show']);
Route::patch('/task/{id}', ['as' => 'showTask', 'uses' => 'TaskController@update']);
Route::post('/task', ['as' => 'createTask', 'uses' => 'TaskController@store']);
