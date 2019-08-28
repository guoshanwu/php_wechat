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
        $input = request()->input();
        dd($input);
        return response()->json(['token' => session('wechat.oauth_user.default.token')]);
    }

}
