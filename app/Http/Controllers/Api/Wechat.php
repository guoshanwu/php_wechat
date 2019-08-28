<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class Wechat extends Controller
{
    /**
     * 微信授权
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        header('Access-Control-Allow-Origin: *');
        return response()->json(['token' => session('wechat.oauth_user.default.token')]);
    }

}
