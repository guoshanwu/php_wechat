<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
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
            return Response()->jsonp($callback, ['error' => 1, 'msg' => $message ? $message : '失败']);

        $headers = ['content-type' => 'application/json'];
        return Response()->json(['error' => 1, 'msg' => $message ? $message : '失败'])
            ->withHeaders($headers);
    }

    /**
     * send success json string
     * @param array $data
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function sendJson($data = []){
        $method   = $this->request->input('method');
        $callback = $this->request->input('callback');

        if($method === 'jsonp' && $callback)
            return Response()->jsonp($callback, ['error' => 0, 'data' => $data, 'msg' => '']);

        $headers = ['content-type' => 'application/json'];
        return Response()->json(['error' => 0, 'data' => $data, 'msg' => ''])
            ->withHeaders($headers);
    }

    /**
     * 分页查询
     * @return array
     */
    public function getPage() {
        $page = $this->request->input('page', 1);
        $list_rows = $this->request->input('limit', 10);
        return [
            'list_rows' => $list_rows,
            'page' => $page
        ];
    }

    /**
     * 搜索框
     * @return mixed
     */
    public function getKeywords() {
        $keywords = request()->param('search_text', '', 'trim');
        return $keywords;
    }
}
