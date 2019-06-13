<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class VoteUser extends Model
{
    //自动补充时间
    public $timestamps = true;
    //模型日期列的存储格式
    protected $dateFormat = 'Y-m-d H:i:s';

    public static function getList($where, $field, $page){
        return self::field($field)->where($where)->limit($page)->get();
    }

}
