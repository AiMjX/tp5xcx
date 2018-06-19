<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/1
 * Time: 12:31
 */

namespace app\api\model;


class Product extends BaseModel
{
    protected $hidden = ['main_img_id', 'from','category_id','head_img_id','pivot','update_time','delete_time','create_time'];

    public function getMainImgUrlAttr($value,$date)
    {
        return self::prefixImgUrl($value,$date);
    }

    public static function getRecentProduct ($conut)
    {
        $product = self::limit($conut)
            ->order('create_time DESC')
            ->select();
        return $product;

    }

    public function iMgs () {
        return self::hasMany('ProductImage','product_id','id');
    }

    public function properties () {
        return self::hasMany('ProductProperty','product_id','id');
    }

    public static function getProductsByCategoryId ($id)
    {
        $result = self::where(['category_id'=>$id])
            ->select();
        return $result;
    }

    public static function getProductDetail ($id) {

        $product = self::with(['properties'])
            ->with(['iMgs'=>function ($query) {

                $query->with(['imgUrl'])->order('order asc');

            }])->find($id);

        return $product;

    }

}