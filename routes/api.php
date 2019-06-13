<?php
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
Route::group(['namespace' => 'Api', 'middleware' => 'wechat.oauth:snsapi_base'], function () {
    Route::get('votelist', 'VoteController@voteList');  //投票列表
});

Route::group(['namespace' => 'Api'], function(){
    Route::post('upload', 'UploadController@upload');   //图片上传
});


