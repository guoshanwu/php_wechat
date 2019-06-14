<?php

namespace App\Http\Controllers\Api;

use App\Model\Image;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Upload extends Controller
{
    public function upload(Request $request, Image $image){
        $file = $request->file('image');
        //判断文件是否上传成功
        if ($file->isValid()){
            $ext = $file->getClientOriginalExtension(); //文件后缀
            $savename = md5(uniqid()) . '.' . $ext; //保存名称
            $url = '/uploads/' . date('Ymd') . '/' . $savename; //图片地址
            //图片上传
            $bool = Storage::disk('uploadimg')->put($url, file_get_contents($file->getRealPath()));
            if (!$bool){
                return $this->sendError('上传失败'.$file->getErrorMessage());
            }
            $image->name = $file->getClientOriginalName(); //原始文件名
            $image->savename = $savename;
            $image->url = $url;
            $image->ext = $ext;
            $image->mimetype = $file->getClientMimeType();   //mime类型
            $image->size = $file->getClientSize();   //文件大小
            $result = $image->save();
            if ($result){
                return response()->json(['code' => 1, 'data' => ['id' => $image->id, 'url' => storage_path(env('UPLOADIMG')).$url]]);
            }
        }
        return response()->json(['code' => -1, 'msg' => '上传失败'.$file->getErrorMessage()]);
    }
}
