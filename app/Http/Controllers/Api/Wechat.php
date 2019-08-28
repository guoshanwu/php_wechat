<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
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
        // 通过 code 获取 open_id
        $client = new Client();
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WECHAT_OFFICIAL_ACCOUNT_APPID').'&secret='.env('WECHAT_OFFICIAL_ACCOUNT_SECRET').'&code='.$code.'&grant_type=authorization_code';
        try {
            $result = $client->request('GET', $url, ['timeout' => 1.5]);
            $result = $result->getBody();
            $result = json_decode($result);
            //存入到session
            session(['access_token' => $result['access_token'], 'openid' => $result['openid']]);
            return response()->json(['code' => 1, 'access_token' => $result['access_token']]);
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['code' => -1, 'msg' => $e->getMessage()]);
        }
    }

}
