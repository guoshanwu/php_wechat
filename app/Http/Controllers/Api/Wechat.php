<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use http\Client;
use Illuminate\Support\Facades\Log;

class Wechat extends Controller
{
    /**
     * 微信授权
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        header('Access-Control-Allow-Origin: *');
        $code = request()->input('code');
        // 判断是否在微信中
        $ua = request()->header('User-Agent');
        $is_in_wechat = preg_match('/MicroMessenger/i', $ua);
//        if ($is_in_wechat && !$user->open_id) {
            // 用户在微信中, 并且没有关联 openid
            // 通过 code 获取 open_id
            $client = new Client();
            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WECHAT_APPID').'&secret='.env('WECHAT_SECRET').'&code='.$code.'&grant_type=authorization_code';
            try {
                $result = $client->request('GET', $url, ['timeout' => 1.5]);
                dd($result);
            } catch(\Exception $e) {
                Log::error($e->getMessage());
                dd($e->getMessage());
            }
//        }
    }

}
