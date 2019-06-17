<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class Vote extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * 新增数据
     * @return array
     */
    public function storeRules(){
        return [
            'images_ids' => 'required|array',
            'name' => 'required',
            'mobile' => 'required'
        ];
    }

    /**
     * 更新数据
     * @return array
     */
    public function updateRules(){
        return [
            'id' => 'required',
            'openid' => 'required',
            'images_ids' => 'required|array',
            'name' => 'required',
            'mobile' => 'required'
        ];
    }

    /**
     * 验证器错误的自定义消息
     * @return array
     */
    public function messages(){
        return [
            'required' => ':attribute不能为空',
            'array' => ':attribute必须是数组',
        ];
    }

    /**
     * 自定义字段名称，提示的时候中文,如果没有这个，提示的就是input的name
     * @return array
     */
    public function customAttributes(){
        return [
            'images_ids' => '图片',
            'name' => '客户姓名',
            'mobile' => '联系号码'
        ];
    }

    /**
     * 新增数据 验证
     * @param $request
     * @return array
     */
    public function storeValidator($request){
        $validator = Validator::make($request, $this->storeRules(), $this->messages(), $this->customAttributes());
        if ($validator->fails()){
            return ['code' => -1, 'msg' => $validator->errors()->first()];
        }
        return ['code' => 1];
    }

    /**
     * 更新数据 验证
     * @param $request
     * @return array
     */
    public function updateValidator($request){
        $validator = Validator::make($request, $this->updateRules(), $this->messages(), $this->customAttributes());
        if ($validator->fails()){
            return ['code' => -1, 'msg' => $validator->errors()->first()];
        }
        return ['code' => 1];
    }

}
