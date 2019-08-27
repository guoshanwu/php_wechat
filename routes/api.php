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
Route::group(['namespace' => 'Api', 'middleware' => ['wechat.oauth:snsapi_base']], function () {
    Route::any('index', 'Index@index'); //展示前端
});

Route::group(['namespace' => 'Api'], function(){
    Route::get('getOpenid', 'Index@getOpenid');

    Route::get('Banner', 'Banner@index');   //Banner
    Route::get('Vote', 'Vote@index');   //列表
    Route::post('Vote/castVote', 'Vote@castVote');  //点击投票
    Route::post('Upload/upload', 'Upload@upload');   //图片上传
});


