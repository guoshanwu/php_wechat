<?php

namespace App\Http\Controllers\Api;

use App\Model\Banners;

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
        session(['test' => 'sdsdsd123']);
        $result = Banners::select('id', 'savename', 'url')->where('status', 1)->orderBy('sort', 'desc')->get();
        foreach($result as $k => $v){
            $result[$k]['url'] = env('OSSURL').$v['url'];
        }
        return $this->sendSuccess($result);
    }
}
