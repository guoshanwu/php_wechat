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
            $user = new Users();
            $userInfo = $user->find($this->openid);
            !empty($userInfo) && $user->openid = $this->openid;  //第一次登陆
            if ($user->save()){
                return $next($request);
            }
        });
    }

}
