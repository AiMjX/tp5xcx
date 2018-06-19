<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/1
 * Time: 15:24
 */

namespace app\api\controller\v1;


use app\api\validate\Count;
use app\api\model\Product as ProductModel;
use app\api\validate\IDMustBePostivenInt;
use app\lib\exception\ProductException;

class Product
{
    // 获取最近 新商品
    /**
     * @param int $count
     * @return $this|false|\PDOStatement|string|\think\Collection
     * @throws ProductException
     * @throws \app\lib\exception\ParameterException
     * @url api/:version/product/recent
     */
    public function getRecentProduct ( $count = 15)
    {
        (new Count())->goCheck();
        $result = ProductModel::getRecentProduct($count);
        if ( !$result )
        {
            throw new ProductException();
        }
        $collection = collection($result);
        $result = $collection->hidden(['summary']);
        return $result;
    }

    /**
     * @param $id  对应的分类id  的所属 商品
     * @return $this|false|\PDOStatement|string|\think\Collection
     * @throws ProductException
     * @throws \app\lib\exception\ParameterException
     * @url api/:version/product/by_category
     */
    public function getAllProductInCategory ( $id )
    {
        (new IDMustBePostivenInt())->goCheck();
        $result = ProductModel::getProductsByCategoryId($id);
        if (!$result) {
            throw new ProductException();
        }
        // 临时 隐藏返回字段，将资源转换
        $collection = collection($result);
        //dump($collection);
        $result = $collection->hidden(['summary']);
        return $result;
    }

    /**
     * @param $id
     * @throws \app\lib\exception\ParameterException
     * @url api/:version/product/:id
     */
    public function getOne ($id) {
        (new IDMustBePostivenInt())->goCheck();

        $product = ProductModel::getProductDetail($id);

        if (!$product) {
            throw  new ProductException();
        }

        return $product;
    }

}