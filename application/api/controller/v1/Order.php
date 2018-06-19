<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/4
 * Time: 22:05
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\Token as TokenService;
use app\api\validate\IDMustBePostivenInt;
use app\api\validate\OrderPlace;

use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use app\api\validate\PagingParameter;
use app\lib\exception\OrderException;

class Order extends BaseController
{
    // 用户选择商品后，向api提交包含所选择商品的相关信息
    // Api 在接收到信息后，需要检查订单相关商品库存量
    // 有库存，把订单存入数据库中= 下单成功了，返回客户端消息
    // 调用支付接口，进行支付
    // 还需要再次进行库存量检测
    // 服务器这边就可以调用微信支付接口进行支付
    // 小程序根据服务器返回结果拉起微信支付
    // 微信会返回给我们一个支付结果
    // 成功：也需要进行库存量检测
    // 成功：进行库存量扣除  失败：返回一个支付失败结果

    // TP5 前置操作，
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getDetail,getSummaryByUser'],
    ];

    public function placeOrder ()
    {

        (new OrderPlace())->goCheck();

        $product = input('post.products/a');

        $uid = TokenService::getCurrentUid();

        $result = (new OrderService())->place($uid,$product);

        return $result;
        
    }

    public function getSummaryByUser ($page = 1, $size=15) {

        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentUid();
        $result = OrderModel::getSummaryByUser($uid,$page,$size);
        if ($result->isEmpty()) {
            return [
                'data' => [],
                'current_page' =>$result->getCurrentPage()
            ];
        }
        $data = $result->hidden(['snap_items','prepay_id','snap_address'])->toArray();
        return [
            'data' => $data,
            'current_page' =>$result->getCurrentPage()
        ];
    }

    public function getDetail ( $id) {
        (new IDMustBePostivenInt())->goCheck();
        $orderDetail = OrderModel::get($id);
        if (!$orderDetail) {
            throw new OrderException();
        }
        return $orderDetail->hidden(['perpay_id']);
    }

}