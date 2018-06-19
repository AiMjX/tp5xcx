<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/1
 * Time: 10:36
 */

namespace app\api\model;


use think\Model;

class BaseModel extends Model
{

    protected $hidden = ['delete_time'];


    public function prefixImgUrl ($value,$date)
    {
        $finalUrl = $value;
        if ($date['from'] == 1) {
            $finalUrl =  config('setting.img_prefix').$value;
        }
        return $finalUrl;
    }

}