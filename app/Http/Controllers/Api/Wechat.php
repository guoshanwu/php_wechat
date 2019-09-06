<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Wechat extends Controller
{
    /**
     * 微信授权
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $code = request()->input('code');
        // 通过 code 获取 open_id
        $client = new Client();
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WECHAT_OFFICIAL_ACCOUNT_APPID').'&secret='.env('WECHAT_OFFICIAL_ACCOUNT_SECRET').'&code='.$code.'&grant_type=authorization_code';
        try {
            $result = $client->request('GET', $url, ['timeout' => 1.5]);
            $result = $result->getBody();
            $result = json_decode($result, true);
            $openid = $result['openid'];    //openid
            //更新用户信息
            $userInfo = User::where(['openid' => $openid])->first();
            if (empty($userInfo)){
                //第一次授权,新增用户
                $userModel = new User();
                $userModel->openid = $openid;
                $userModel->save();
            }else{
                //已授权,更新登陆时间
                $userInfo->updated_at = date('Y-m-d H:i:s');
                $userInfo->save();
            }
            return response()->json(['code' => 1, 'openid' => $result['openid']]);
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['code' => -1, 'msg' => $e->getMessage()]);
        }
    }

}
