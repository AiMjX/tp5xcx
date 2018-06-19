<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/5/31
 * Time: 13:18
 */

namespace app\api\model;


class Banner extends BaseModel
{
    public function items()
    {
        // 关联模型  是BannerItem
        return $this->hasMany('BannerItem', 'banner_id', 'id');

    }

    // 多个关联的话 就再定义一个
    /*public function imgs () {
        // 关联模型  是BannerItem
        return $this->hasMany('imgs','banner_id','id');
    }
    $banner = self::with(['items','imgs'])->find($id);*/  // 这样子去查询


    public static function getBannerById($id)
    {
        /*$res = self::get($id);
        $banner = Db::query('select * from banner_item where banner_id=?', [$id]);   // 原生查询
        $banner = Db::table('banner_item')->where(['banner_id' => $id])->select();
        $banner = Db::name('banner_item')// 闭包写法
        ->where(function ($query) use ($id) {
            $query->where(['banner_id' => $id]);
        })
            ->select();*/
        $banner = self::with(['items', 'items.img'])->find($id);

        return $banner;
    }

}