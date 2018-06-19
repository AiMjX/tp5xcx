<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/4
 * Time: 12:13
 */

namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden = ['delete_time'];
    // theme  和 images  是一对一关系
    // theme 里面有外键 所以用belongsTo  hasOn
    public function imgUrl ()
    {
        return self::belongsTo('Image','img_id','id');
    }
}