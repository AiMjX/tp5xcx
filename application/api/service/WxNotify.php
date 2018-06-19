<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/7
 * Time: 21:47
 */

namespace app\api\service;

use app\api\model\Product;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use think\Log;

Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');

class WxNotify extends \WxPayNotify
{

    public function NotifyProcess($data, &$msg)
    {
        if ($data['result_code'] == 'SUCCESS') {
            $orderNo = $data['out_trade_no'];
            Db::startTrans(); // 开启事物
            try {
                $order = OrderModel::where(['order_no'=>$orderNo])->lock(true)->find();
                if ($order['status'] == 1) {
                    $service = new OrderService();
                    $stockStatus = $service->checkOrderStock($order->id);
                    if ($stockStatus['pass']) {
                        self::updateOrderStatus($order->id,true);
                        $this->reduceStock($stockStatus);
                    }else
                    {
                        $this->updateOrderStatus($order->id,false);
                    }
                    Db::commit();  // 提交事物
                    return true;
                }
            } catch (Exception $exception) {
                Db::rollback();  // 回滚事物
                Log::record($exception);
                return false;
            }
        }else {
            return true;
        }
    }
    // 减库存量
    private function reduceStock ($stockStatus) {
        foreach ($stockStatus['pStatusArray'] as $singlePStatus) {
            Product::where(['id'=>$singlePStatus['id']])->setDec('stock',$singlePStatus['count']);
        }
    }
   // 改数据库状态
    private function updateOrderStatus ($orderID,$success) {
        $status = $success?OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where(['id'=>$orderID])->update(['status'=>$status]);
    }

}