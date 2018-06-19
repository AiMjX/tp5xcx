<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/1
 * Time: 12:15
 */

namespace app\api\controller\v2;


class Banner
{
    public function __construct()
    {
        echo '走你，第二版本';
    }

    public function getBanner ($id) {
        echo $id;
    }

}