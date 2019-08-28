<?php

namespace App\Http\Controllers\Api;

use App\Model\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class Base extends Controller
{
    protected $request;
    protected $openid;
    public function __construct(Request $request){  //每次登陆都更新
        $this->request = $request;
        $token = $this->request->header('token');
        header('Access-Control-Allow-Origin:*');
        $openid = Session::get('openid');
        dd($openid);
        if (empty($token) || $token != session('access_token')){
            return redirect('http://web.tenstudio.top');    //重新授权
        }
        $this->openid = session('openid');
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

        return Response()->json(['code' => -1, 'msg' => $message ? $message : '失败']);
    }

    /**
     * send success json string
     * @param array $data
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function sendSuccess($data = [], $message = '成功'){
        $method   = $this->request->input('method');
        $callback = $this->request->input('callback');

        if($method === 'jsonp' && $callback)
            return Response()->jsonp($callback, ['code' => 1, 'data' => $data, 'msg' => $message]);

        return Response()->json(['code' => 1, 'data' => $data, 'msg' => $message]);
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
            $data[] = env('OSSURL') . Image::where('id', $v)->value('url');
        }
        return $data;
    }

}
