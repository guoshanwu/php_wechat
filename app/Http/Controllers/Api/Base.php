<?php

namespace App\Http\Controllers\Api;

use App\Model\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;

class Base extends Controller
{

    protected $request;
    protected $openid;
    public function __construct(Request $request){  //每次登陆都更新
        $this->request = $request;
        $this->middleware(function($request, $next){
//	        $this->openid = session('wechat.oauth_user.default.id');
            $this->openid = 'o2nfu56MsF1W4nUyX1aQgp8k_fi0';
            $user = User::find($this->openid);
            if (empty($user)){
                //第一次登陆,新增用户
                $user = new User();
                $user->openid = $this->openid;
            }else{
                //已存在,更新登陆时间
                $user->updated_at = date('Y-m-d H:i:s');
            }
            if ($user->save()){
                return $next($request);
            }
        });
    }

    /**
     * send error json string
     * @param int $code
     * @param string $message
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function sendError($message = ''){
        $method   = $this->request->input('method');
        $callback = $this->request->input('callback');

        if($method === 'jsonp' && $callback)
            return Response()->jsonp($callback, ['code' => -1, 'msg' => $message ? $message : '失败']);

        $headers = ['content-type' => 'application/json'];
        return Response()->json(['code' => -1, 'msg' => $message ? $message : '失败'])
            ->withHeaders($headers);
    }

    /**
     * send success json string
     * @param array $data
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function sendSuccess($data = []){
        $method   = $this->request->input('method');
        $callback = $this->request->input('callback');

        if($method === 'jsonp' && $callback)
            return Response()->jsonp($callback, ['code' => 1, 'data' => $data]);

        $headers = ['content-type' => 'application/json'];
        return Response()->json(['code' => 1, 'data' => $data])
            ->withHeaders($headers);
    }

    /**
     * 码数
     * @return array
     */
    public function getPage() {
        //页码只需要在page参数赋值就会自动获取(page=1)
        //所以只需要获取每页码数就可以
        $listRows = $this->request->input('limit', 10);
        return $listRows;
    }

    /**
     * 搜索框
     * @return mixed
     */
    public function getKeywords() {
        $keywords = $this->request->input('search_text', '');
        return $keywords;
    }

    /**
     * 获取图片url
     * @param $ids
     * @return array|string
     */
    public function getImagesUrl($ids){
        if (!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $data = [];
        foreach($ids as $v){
            $data[] = storage_path(env('UPLOADIMG')) . Image::where('id', $v)->value('url');
        }
        return $data;
    }

}
