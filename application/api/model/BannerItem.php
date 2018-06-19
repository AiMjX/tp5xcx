<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/5/31
 * Time: 21:52
 */

namespace app\api\model;


class BannerItem extends BaseModel
{
    protected $hidden = ['id', 'img_id', 'banner_id', 'delete_time'];
    public function img () {
        return $this->belongsTo('Image','img_id','id');
    }

}