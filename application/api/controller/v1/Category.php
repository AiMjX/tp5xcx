<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/1
 * Time: 18:18
 */

namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;


class Category
{
    /**
     * @url api/:version/category/all
     *  获取所有 类别 以及类别下的头图
     */
    public function getAllCategory () {

        $result = CategoryModel::getAllCategory();
        if ( !$result ) {
            throw new CategoryException();
        }
        return $result;

    }

}