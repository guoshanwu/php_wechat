<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class Index extends Controller
{
    public function index(){
        dd(session('wechat.oauth_user.default'));
    }

    public function getOpenid(){
        dd(session('wechat.oauth_user.default'));
    }

}
