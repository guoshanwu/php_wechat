<?php

namespace App\Http\Controllers\Api;

class Wechat extends Base
{
    /**
     * @api {get} api/Banner/index  微信授权
     */
    public function index(){
        //这个echostr呢  只有说验证的时候才会echo  如果是验证过之后这个echostr是不存在的字段了
        if($_GET['echostr']){
            $echoStr = $_GET["echostr"];
            if ($this->checkSignature()) {
                ob_clean();//防止之前缓存区数据影响
                echo $echoStr;
                exit;
            }
        }else{
            $this->responseMsg(); //如果没有echostr，则返回消息
        }
    }

    //验证微信开发者模式接入是否成功
    private function checkSignature()
    {
        //signature 是微信传过来的签名
        $signature = $_GET["signature"];
        //微信发过来的时间戳
        $timestamp = $_GET["timestamp"];
        //微信传过来的值随机字符串
        $nonce     = $_GET["nonce"];
        //定义你在微信公众号开发者模式里面定义的token 这里举例为weixin
        $token  = "shiguangying";
        //三个变量 按照字典排序 形成一个数组
        $tmpArr = array(
            $token,
            $timestamp,
            $nonce
        );
        // 字典排序
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        //哈希加密  在laravel里面是Hash::
        $tmpStr = sha1($tmpStr);
        //哈希加密后的数据 和微信服务器传过来的签名比较
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @name 消息接收
     * @author weikai
     */
    public function responseMsg()//执行接收器方法
    {
        //获取微信服务器的XML数据 转化为对象 判断消息类型
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            switch($RX_TYPE){
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->handleText($postObj);
                    break;
            }
            echo $result;
        }else{
            echo "";
            exit;
        }
    }
}
