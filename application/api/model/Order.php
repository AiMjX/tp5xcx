<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/7
 * Time: 12:15
 */

namespace app\api\model;


class Order extends BaseModel
{
    // 隐藏字段
    protected $hidden = array(
        'user_id','delete_time','update_time',
    );

    protected $autoWriteTimestamp= true;

    public function getSnapItemsAttr ($value) {
        if (!$value) {
            return null;
        }
        return json_decode(($value));
    }

    public function getSnapAddressAttr($value){
        if(empty($value)){
            return null;
        }
        return json_decode(($value));
    }

    public static function getSummaryByUser ($uid,$page=1,$size=15) {

        $result = self::where(['user_id'=>$uid])
            ->order('create_time DESC')
            ->paginate( $size,true,['page'=>$page,] );

        return $result;
    }


}