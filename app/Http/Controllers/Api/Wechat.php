<?php

namespace App\Http\Controllers\Api;

class Wechat extends Base
{
    /**
     * @api {get} api/Banner/index  微信授权
     */
    public function index(){
        dd('wechat_auth');
    }
}
