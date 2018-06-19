<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/1
 * Time: 18:18
 */

namespace app\api\model;


class Category extends BaseModel
{

    protected $hidden = ['update_time','delete_time'];

    public function img()
    {
        return $this->belongsTo('Image','topic_img_id','id');
    }

    public static function getAllCategory () {
        $result = self::all([],'img');
        return $result;
    }

}