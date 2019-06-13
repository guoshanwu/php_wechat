<?php

namespace App\Http\Controllers\Api;

use App\Model\VoteUser;

class VoteController extends BaseController
{
    /**
     * 处理微信的请求消息
     */
    public function voteList(){
        $where['status']  = 1;
//        $this->getKeywords() && $where['name'] = $this->getKeywords();  //搜索
        $page = $this->getPage();   //分页
        $field = 'id, openid, num, name, remark';
        $result = VoteUser::where($where)->select($field)->paginate($page)->get();
        return $this->sendJson($result);
    }


}
