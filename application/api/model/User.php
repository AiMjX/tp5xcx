<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/3
 * Time: 14:51
 */

namespace app\api\model;


class User extends BaseModel
{

    public function address (){  // user 表没有外键 用hasOne  有的话就用belongsTo
        return $this->hasOne('UserAddress','user_id','id');
    }


    public static function getByOpenID ($openid) {
        $user = self::where(['openid'=>$openid])->find();
        return $user;
    }



}