<?php

namespace App\Http\Controllers\Api;

use App\Model\Images;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function upload(Images $images){
        $file = $this->request->file('image');
        //判断文件是否上传成功
        if ($file->isValid()){
            $ext = $file->getClientOriginalExtension(); //文件后缀
            $savename = md5(uniqid()) . '.' . $ext; //保存名称
            $url = '/' . date('Ymd') . '/' . $savename; //图片地址
            //图片上传
            $bool = Storage::disk('uploadimg')->put($url, file_get_contents($file->getRealPath()));
            if (!$bool){
                return $this->sendError('上传失败'.$file->getErrorMessage());
            }
            $images->name = $file->getClientOriginalName(); //原始文件名
            $images->savename = $savename;
            $images->url = $url;
            $images->ext = $ext;
            $images->mimetype = $file->getClientMimeType();   //mime类型
            $images->size = $file->getClientSize();   //文件大小
            $result = $images->save();
            if ($result){
                return $this->sendJson(['id' => $images->id, 'url' => storage_path(env('UPLOADIMG')).$url]);
            }
        }
        return $this->sendError('上传失败'.$file->getErrorMessage());
    }
}
