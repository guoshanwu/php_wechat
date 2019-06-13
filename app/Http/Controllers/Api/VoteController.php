<?php

namespace App\Http\Controllers\Api;

class VoteController extends BaseController
{
    /**
     * 处理微信的请求消息
     */
    public function voteList(){
	dd(111);
        $this->getKeywords() && $where['name'] = $this->getKeywords();
        $this->getPage();

    }


}
