<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/6
 * Time: 9:08
 */

namespace app\api\service;


use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use app\api\model\Order as OrderModel;
use think\Db;
use think\Exception;

class Order
{
    // 订单的商品列表，也就是客户端传递过来的products参数
    protected $oProducts;
    // 真实的商品信息
    protected $products;
    protected $uid;

    public function place ($uid,$oProducts) {
        //oProducts 和 products 作对比
        // products 从数据库中查询出来
        $this->oProducts = $oProducts;
        $this->products = self::getProductsByOrder($oProducts);
        $this->uid = $uid;
        $status = $this->getOrderStatus();
        if (!$status['pass']) {
            $status['order_id'] = -1;
            return $status;
        }

        // 开始创建订单快照
        $orderSnap = $this->snapOrder ($status);
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;
    }

    // 生成订单
    protected function createOrder ($snap/*快照信息*/) {
        Db::startTrans();  // 开启事物
        try {
            $orderNo = self::makeOrderNo();// 订单号；

            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);
            $order->save();

            // 存数据进 order_product 表
            $orderID = $order->id;
            $create_time = $order->create_time;
            foreach ($this->oProducts as &$p) {
                $p['order_id'] = $orderID;
            }

            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);

            Db::commit(); // 执行事物

            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'order_time' => $create_time,
            ];
        } catch (Exception $exception) {
            Db::rollback();  // 回滚
            throw $exception;
        }

    }

    // 生成唯一的订单号----->对应每个商品订单
    public function makeOrderNo ()
    {
        $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $orderSn = $yCode[intval(date('Y')) - 2017].strtoupper(dechex(date('m'))).date('d').substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
        return $orderSn;
    }

    // 生成订单快照
    private function snapOrder ($status)
    {
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' =>[],
            'snapAddress' => null,
            'snapName' => '',
            'snapImg' => '',
        ];

        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];
        $snap['snapAddress'] = json_encode($this->getUserAddress());

        if (count($this->products) > 1) {
            $snap['snapName'] .= '等多个';
        }

    }



    private function getUserAddress () {
        $userAddress = UserAddress::where(['user_id'=>$this->uid])->find();
        if (!$userAddress) {
            throw new UserException([
                'msg' => '用户收获地址不存在，下单失败',
                'errorCode' => 60001,
            ]);
        }
    }

    // 宫外不调用检测库存量
    public function checkOrderStock ($orderID)
    {
        $oProducts = OrderProduct::where(['order_id'=>$orderID])->select();
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();
        return $status;
    }


    // 库存量和 传递的坐对比
    private function getOrderStatus () {
        $status = ['pass'=>true,'totalCount'=>0 ,'orderPrice'=>0,'pStatusArray' => []];

        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus(
                $oProduct['product_id'],$oProduct['count'],$this->products
            );
            if (!$pStatus['haveStock']) $status['pass'] = false;
            $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['count'];
            array_push($status['pStatusArray'],$pStatus);
        }
        return $status;
    }

    private function getProductStatus ($oPID,$oCount,$products) {
        $pStatus = [
            'id' => null,
            'haveStock' => false,
            'count' => 0,
            'name' => '',
            'totalPrice' => '',
        ];

        for ($i=0; $i<count($products);$i++) {
            if ($oPID == $products[$i]['id']) {
                $pIndex = $i;
            }
        }

        if ($pIndex == -1) {
            throw new OrderException(['msg'=> "id为{$oPID}商品部存在，创建订单失败"]);
        }
        else {
            $product = $products[$pIndex];
            $pStatus['id'] = $products['id'];
            $pStatus['name'] = $products['name'];
            $pStatus['count'] = $oCount;
            $pStatus['totalPrice'] = $products['price'] * $oCount;
            if ($product['stock'] - $oCount >=0) {
                $pStatus['haveStock'] = true;
            }
            return $products;
        }
    }



    /**
     * 根据订单信息查找真实商品信息
     */
    private function getProductsByOrder ($products) {
        /*foreach () {
            不能循环查询数据库  不可取的千万别
        }*/
        $oPIDs = [];
        foreach ( $products as $item) {
            array_push($oPIDs,$item['product_id']);
        }
        $products = Product::all($oPIDs)
        ->visible(['id','price','stock','name','main_img_url'])
        ->toArray();
        return $products;

    }

}