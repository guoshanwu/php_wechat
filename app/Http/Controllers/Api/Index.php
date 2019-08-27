<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class Index extends Controller
{
    /**
     * 微信授权 返回token给前端
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        return response()->json(['code' => 1, 'token' => 'wechat.oauth_user.default.token']);
    }

    public function mssg(){
        dd('successs');
    }

}
