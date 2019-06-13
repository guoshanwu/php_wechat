<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Users;

class BaseController extends Controller
{
    protected $openid;

    public function __construct(){
        $this->middleware(function($request, $next){
            $this->openid = session('wechat.oauth_user.default.id');
            $userInfo = Users::find($this->openid);
            dd($userInfo);
            return $next($request);
        });
    }

}
