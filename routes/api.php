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
Route::group(['namespace' => 'Api'], function () {
    Route::any('Wechat/index', 'Wechat@index'); //微信授权
    Route::get('Banner/index', 'Banner@index');   //Banner
    Route::get('Vote/index', 'Vote@index');   //列表
    Route::post('Vote/castVote', 'Vote@castVote');  //点击投票
    Route::post('Upload/upload', 'Upload@upload');   //图片上传
    Route::post('Vote', 'Vote@store');   //提交(参加活动)
});