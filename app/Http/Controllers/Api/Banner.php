<?php

namespace App\Http\Controllers\Api;

use App\Model\Banners;
use App\Model\User;

class Banner extends Base
{
    /**
     * @api {get} api/Banner/index  轮播图
     *
     * @apiSuccess {int}  id ID
     * @apiSuccess {string} savename   图片名称
     * @apiSuccess {string} url 图片链接
     *
     */
    public function index(){
        $userInfo = User::where(['opneid' => 'o2nfu56MsF1W4nUyX1aQgp8k_fi0'])->first();
        dd($userInfo);
        $result = Banners::select('id', 'savename', 'url')->where('status', 1)->orderBy('sort', 'desc')->get();
        foreach($result as $k => $v){
            $result[$k]['url'] = env('OSSURL').$v['url'];
        }
        return $this->sendSuccess($result);
    }
}
