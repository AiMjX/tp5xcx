<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/1
 * Time: 10:08
 */

namespace app\api\model;


class Image extends BaseModel
{
    protected $hidden = ['id', 'from','update_time','delete_time'];


    // 获取器来自动完成  get Url 为字段 Attr
    public function getUrlAttr($value,$date)
    {

        return self::prefixImgUrl($value,$date);

    }


}