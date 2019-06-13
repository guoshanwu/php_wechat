<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    //自动补充时间
    public $timestamps = true;
    //模型日期列的存储格式
    protected $dateFormat = 'Y-m-d H:i:s';
}
