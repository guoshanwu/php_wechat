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
            $user->openid = $this->openid;  //新增或更新用户信息
            if ($user->save()){
                return $next($request);
            }
        });
    }

}
