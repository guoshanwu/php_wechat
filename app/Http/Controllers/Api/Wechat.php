<?php

namespace App\Http\Controllers\Api;

class Wechat extends Base
{
    /**
     * @api {get} api/Banner/index  微信授权
     */
    public function index(){
        $redirecUrl = $this->request->input('redirect_url');
        $appId = env('WECHAT_OFFICIAL_ACCOUNT_APPID');//获取自己公众号的 appid
        $redirectUri = urlencode($redirecUrl);//处理url
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appId."&redirect_uri=".$redirectUri."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
        header('location:'.$url);
    }

}
