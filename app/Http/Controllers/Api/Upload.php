<?php

namespace App\Http\Controllers\Api;

use App\Model\Image;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OSS\OssClient;
use OSS\Core\OssException;

class Upload extends Controller
{
    /**
     * @api {post} api/Upload/upload  上传[api/Upload/upload]
     * @apiName upload
     * @apiGroup Upload
     * @apiSampleRequest api/Upload/upload
     *
     * @apiParam {string}  file 图片/文件
     *
     */
    public function upload(Request $request){
        $file = $request->file('file');
        if (empty($file)){
            return response()->json(['code' => -1, 'msg' => '上传文件不能为空']);
        }
        return response()->json($this->comUpload($file));
    }

    /**
     * 上传图片
     * @param $file
     * @param string $path  路径,默认uploads/..
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function comUpload($file, $path = 'uploads/'){
        //判断文件是否上传成功
        if ($file->isValid()){
            $ext = $file->getClientOriginalExtension(); //文件后缀
            $name = md5(uniqid());
            $savename = $name . '.' . $ext; //保存名称
            $url = $path . date('Ymd') . '/' . $savename; //图片地址   //图片上传到阿里云,路径不能以'/'开头
            //图片上传
            $bool = Storage::disk('uploadimg')->put($url, file_get_contents($file->getRealPath()));
            if (!$bool){
                return response()->json(['code' => -1, 'msg' => '上传失败'.$file->getErrorMessage()]);
            }
            //图片上传到阿里云
            $pathUrl = storage_path(env('UPLOADIMG').$url); //图片绝对路径
            $ossResult = $this->OssClient($url, $pathUrl);
            unlink($pathUrl);   //删除本地图片
            if ($ossResult['code'] == -1){
                return response()->json(['code' => -1, 'msg' => $ossResult['msg']]);
            }
            $image = new Image();
            $image->name = $file->getClientOriginalName(); //原始文件名
            $image->savename = $savename;
            $image->url = $url;
            $image->ext = $ext;
            $image->mimetype = $file->getClientMimeType();   //mime类型
            $image->size = $file->getClientSize();   //文件大小
            $result = $image->save();
            if ($result){
                return ['code' => 1, 'data' => ['id' => $image->id, 'url' => env('OSSURL').$url]];
            }
        }
        return ['code' => -1, 'msg' => '上传失败,'.$file->getErrorMessage()];
    }


    /**
     * @param $object   文件名称
     * @param $filePath 由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt
     * @return array
     */
    public function OssClient($object, $filePath){
        $accessKeyId = env('ACCESS_KEY_ID');
        $accessKeySecret = env('ACCESS_KEY_SECRET');
        $endpoint = env('END_POINT');
        $bucket = env('BUCKET');
        try{
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->uploadFile($bucket, $object, $filePath);
            return ['code' => 1];
        } catch(OssException $e) {
            return ['code' => -1, 'msg' => $e->getMessage()];
        }
    }
}
