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
            $user = Users::find($this->openid);
            if (empty($user)){
		//第一次登陆,新增用户
                $user = new Users();
                $user->openid = $this->openid;
            }else{
		//已存在,更新登陆时间
		$user->updated_at = date('Y-m-d H:i:s');
	    }
            if ($user->save()){
                return $next($request);
            }
        });
    }

}
