<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/1
 * Time: 12:30
 */

namespace app\api\model;


class Theme extends BaseModel
{

    protected $hidden = ['topic_img_id', 'head_img_id','update_time','delete_time'];
    // theme  和 images  是一对一关系
    // theme 里面有外键 所以用belongsTo  hasOn
    public function topicImg ()
    {
        return self::belongsTo('Image','topic_img_id','id');
    }

    public function headImg ()
    {
        return self::belongsTo('Image','head_img_id','id');
    }

    public function products ()
    {
        return self::belongsToMany('Product','theme_product','theme_id','product_id');
    }

    public static function getThemeByIds ($ids)
    {
        $result = self::with('topicImg,headImg')->select($ids);
        return $result;
    }

    public static function getThemeWithProducts($id)
    {
        $result = self::with('headImg,products')->find($id);
        return $result;
    }

}