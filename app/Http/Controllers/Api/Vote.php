<?php

namespace App\Http\Controllers\Api;

use App\Model\Image;
use App\Model\VoteUser;

class Vote extends Base
{
    /**
     * @api {post} api/Vote/index  投票列表[api/Vote/index]
     * @apiName index
     * @apiGroup Vote
     * @apiSampleRequest api/Vote/index
     *
     * @apiParam {int}   [is_ranking=1] 是否排名 1:是(高到低)
     * @apiParam {string}   [search_text]   搜索条件
     * @apiParam {int}  page=1  页码
     * @apiParam {int}  limit=10   码数
     *
     * @apiSuccessExample {json} 返回数据示例:
     * HTTP/1.1 200 OK
     * {
            "code": 1,
            "data": {
                "total": 4,
                "list": [
                    {
                        "id": 2,
                        "openid": "o2nfu56MsF1W4nUyX1aQgp8k_fi0",
                        "num": 20,  //票数
                        "name": "李四",   //姓名
                        "url": "D:\\phpStudy\\PHPTutorial\\WWW\\laravel\\wechat\\storage\\app/public/uploads/20190614/9ff74ce01063e261e1067ccf0641ad79.jpg"
                    },
                    {
                        "id": 1,
                        "openid": "o2nfu56MsF1W4nUyX1aQgp8k_fi1",
                        "num": 10,
                        "name": "张三",
                        "url": "D:\\phpStudy\\PHPTutorial\\WWW\\laravel\\wechat\\storage\\app/public/uploads/20190614/2437347934d3d0c8fd11dacc67e6f6a8.jpg"
                    }
                ]
            }
        }
     */

    public function index(){
        $where['status']  = 1;
        $this->getKeywords() && $where['name'] = $this->getKeywords();  //搜索
        $page = $this->getPage();   //分页
        //排序
        $isRanking = $this->request->input('is_ranking', '');
        $orderBy = 'id asc';
        if (!empty($isRanking) && $isRanking == 1){
            $orderBy = 'num desc';
        }
        $data = VoteUser::select('id', 'openid', 'images_ids', 'num', 'name')
            ->where($where)
            ->orderByRaw($orderBy)  //orderByRaw兼容写法
            ->paginate($page)
            ->toArray();
        //查询图片
        foreach($data['data'] as $k => $v){
            if (!empty($v['images_ids'])){
                $imagesId = explode(',', $v['images_ids']);
                $data['data'][$k]['url'] = $this->getImagesUrl($imagesId[0]);
            }
            unset($data['data'][$k]['images_ids']);
        }
        return $this->sendSuccess(['total' => $data['total'], 'list' => $data['data']]);
    }

    /**
     * @api {post} api/Vote/show  详情[api/Vote/show]
     * @apiName show
     * @apiGroup Vote
     * @apiSampleRequest api/Vote/show
     *
     * @apiParam {int}   id ID
     * @apiParam {string}   openid   openid
     *
     * @apiSuccessExample {json} 返回数据示例:
     * HTTP/1.1 200 OK
     *{
            "code": 1,
            "data": {
                "id": 1,
                "openid": "o2nfu56MsF1W4nUyX1aQgp8k_fi0",
                "num": 10,  //票数
                "name": "张三",
                "remark": "奥术大师付多所多所多所多所多所多",
                "ranking": 3,   //排名
                "images": [
                    "D:\\phpStudy\\PHPTutorial\\WWW\\laravel\\wechat\\storage\\app/public/uploads/20190614/2437347934d3d0c8fd11dacc67e6f6a8.jpg",
                    "D:\\phpStudy\\PHPTutorial\\WWW\\laravel\\wechat\\storage\\app/public/uploads/20190614/9ff74ce01063e261e1067ccf0641ad79.jpg",
                    "D:\\phpStudy\\PHPTutorial\\WWW\\laravel\\wechat\\storage\\app/public/uploads/20190614/bd224672e0ff41ac89da90ffc4db967f.jpg"
                ],
                "is_edit": 1    //是否可以编辑 1:是 -1:否
            },
        }
     */
    public function show(){
        $id = $this->request->input('id');
        $openid = $this->request->input('openid');
        if (empty($id) || empty($openid)){
            return $this->sendError('缺少参数');
        }
        $data = VoteUser::select('id', 'openid', 'images_ids', 'num', 'name', 'remark')
            ->where(['id' => $id, 'openid' => $openid])
            ->first();
        if (empty($data)){
            return $this->sendError('未找到数据');
        }
        $data['ranking'] = VoteUser::where('num', '>', $data['num'])->count() + 1 ;  //排名
        $data['images'] = $this->getImagesUrl($data['images_ids']); //图片
        $data['is_edit'] = -1;  //不可编辑
        if ($this->openid == $openid){
            $data['is_edit'] = 1;   //可以编辑
        }
        unset($data['images_ids']);
        return $this->sendSuccess($data);
    }

}
