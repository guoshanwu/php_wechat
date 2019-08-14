<?php

namespace App\Http\Controllers\Api;

use App\Model\VoteRecord;
use App\Model\VoteUser;
use App\Http\Requests\Vote as VoteValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Vote extends Base
{
    /**
     * @api {get} api/Vote/index  投票列表[api/Vote/index]
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
        $this->getKeywords() && $where['name'] = $this->getKeywords();  //搜索
        $page = $this->getPage();   //分页
        //排序
        $isRanking = $this->request->input('is_ranking', '');
        $orderBy = 'id asc';
        if (!empty($isRanking) && $isRanking == 1){
            $orderBy = 'num desc';
        }
        $where['status']  = 1;
        $data = VoteUser::select('id', 'openid', 'images_ids', 'num', 'name')
            ->where($where)
            ->orderByRaw($orderBy)  //orderByRaw兼容写法
            ->paginate($page)
            ->toArray();
        //查询图片
        foreach($data['data'] as $k => $v){
            if (!empty($v['images_ids'])){
                $imagesId = explode(',', $v['images_ids']);
                $data['data'][$k]['show_url'] = $this->getImagesUrl($imagesId[0])[0];
                $data['data'][$k]['all_url'] = $this->getImagesUrl($imagesId);
            }
            unset($data['data'][$k]['images_ids']);
        }
        return $this->sendSuccess(['last_page' => $data['last_page'], 'list' => $data['data']]);
    }

    /**
     * @api {get} api/Vote/show  详情页面[api/Vote/show]
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

    /**
     * @api {post} api/Vote/store  新建数据[api/Vote/store]
     * @apiName store
     * @apiGroup Vote
     * @apiSampleRequest api/Vote/store
     *
     * @apiParam {array}   images_ids   图片id数组
     * @apiParam {string}   name   姓名
     * @apiParam {string}   mobile   电话号码
     * @apiParam {string}   [remark]   备注
     *
     */

    public function store(){
        try{
            //数据验证
            $validator = (new VoteValidator())->storeValidator($this->request->input());
            if ($validator['code'] == -1){
                return $this->sendError($validator['msg']);
            }
            //一个用户只能参与一次活动
            $userInfo = VoteUser::where('openid', $this->openid)->first();
            if (!empty($userInfo)){
                return $this->sendError('用户信息已存在,不能重复参与活动');
            }
            //新增数据
            $voteUser = new VoteUser();
            $voteUser->openid = $this->openid;
            $voteUser->images_ids = implode(',', $this->request->input('images_ids'));
            $voteUser->name = $this->request->input('name');
            $voteUser->mobile = $this->request->input('mobile');
            $voteUser->remark = $this->request->input('remark');
            if (!$voteUser->save()){
                return $this->sendError('报名失败,请稍后重试!');
            }
            return $this->sendSuccess();
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @api {get} api/Vote/update  编辑数据[api/Vote/update]
     * @apiName update
     * @apiGroup Vote
     * @apiSampleRequest api/Vote/update
     *
     * @apiParam {int}   id   ID
     * @apiParam {string}   openid   openid
     * @apiParam {array}   images_ids   图片id数组
     * @apiParam {string}   name   姓名
     * @apiParam {string}   mobile   电话号码
     * @apiParam {string}   [remark]   备注
     *
     */
    public function update(){
        try{
            //数据验证
            $validator = (new VoteValidator())->updateValidator($this->request->input());
            if ($validator['code'] == -1){
                return $this->sendError($validator['msg']);
            }
            if ($this->openid != $this->request->input('openid')){
                return $this->sendError('只能编辑自己的活动信息');
            }
            $userInfo = VoteUser::where('id', $this->request->input('id'))
                ->where('openid', $this->request->input('openid'))
                ->first();
            if (empty($userInfo)){
                return $this->sendError('未找到数据');
            }
            //更新数据
            $userInfo->images_ids = implode(',', $this->request->input('images_ids'));
            $userInfo->name = $this->request->input('name');
            $userInfo->mobile = $this->request->input('mobile');
            $userInfo->remark = $this->request->input('remark');
            if (!$userInfo->save()){
                return $this->sendError('编辑失败,请稍后重试');
            }
            return $this->sendSuccess();
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @api {post} api/Vote/castVote  投票[api/Vote/castVote]
     * @apiName castVote
     * @apiGroup Vote
     * @apiSampleRequest api/Vote/castVote
     *
     * @apiParam {int}   id   ID
     * @apiParam {string}   openid   openid
     *
     */
    public function castVote(){
        $id = $this->request->input('id');
        $openid = $this->request->input('openid');
        if (empty($id) || empty($openid)){
            return $this->sendError('缺少参数');
        }
        $date = date('Y-m-d');
        $voteInfo = VoteRecord::where(['vote_openid' => $this->openid, 'vote_date' => $date])->first();
        if (!empty($voteInfo)){
            return $this->sendError('您今天已投出你圣神的一票,每个用户每天只能投一票');
        }
        DB::beginTransaction();
        try{
            //投票记录表添加一条数据
            $voteRecord = new VoteRecord();
            $voteRecord->vote_openid = $this->openid;   //投票人id
            $voteRecord->bevote_openid = $openid;       //被投票人id
            $voteRecord->vote_date = $date; //投票时间
            if (!$voteRecord->save()){
                DB::rollBack();
                return $this->sendError('投票失败,请稍后重试');
            }
            //投票数量+1
            $result = VoteUser::where(['id' => $id, 'openid' => $openid])->increment('num');
            if (!$result){
                DB::rollBack();
                return $this->sendError('投票失败,请稍后重试');
            }
            DB::commit();
            return $this->sendSuccess([], '投票成功');
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
            return $this->sendError($e->getMessage());
        }
    }

}
