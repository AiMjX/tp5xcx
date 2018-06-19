<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/7
 * Time: 16:00
 */

namespace app\api\service;


use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use think\Loader;
use think\Log;

Loader::import('WxPay.WxPay',EXTEND_PATH,'Api.php');

class Pay
{
    private $orderID;
    private $orderNO;

    function __construct($orderID)
    {
        if (!$orderID) {
            throw new Exception('订单号不允许为NULL');
        }
        $this->orderID = $orderID;
    }

    //  主方法 支付
    public function pay ()
    {
        // 订单号可能根本就不存在
        // 订单号是存在的，但是，订单号和当前用户是不匹配的。
        // 订单可能已经被支付
        // 库存量检测，
        $this->checkOrderValid();
        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->orderID);
        if (!$status['pass']) {
            return $status;
        }
        // 通过了就发起支付

        return $this->makeWxPreOrder($status['orderPrice']);

    }

    private function makeWxPreOrder ($totalPrice)
    {
        // openid
        $openid = Token::getCurrentTokenVar('openid');
        if (!$openid) {
            throw new TokenException();
        }
        $wxOrderDate = new \WxPayUnifiedOrder();
        $wxOrderDate->SetOut_trade_no($this->orderNO); // 商户系统内部订单号
        $wxOrderDate->SetTrade_type('JSAPI');  // 交易类型
        $wxOrderDate->SetTotal_fee($totalPrice*100); // 支付总金额
        $wxOrderDate->SetBody('Woo商贩'); // 商品或支付单简要描述
        $wxOrderDate->SetOpenid($openid);  // 用户身份 openid
        $wxOrderDate->SetNotify_url( config('secure.pay_back_url') ); // 接收微信支付异步通知回调地址
        return $this->getPayDignature($wxOrderDate);
    }

    // 预订单
    private function getPayDignature ($wxOrderData)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData); // 统一下单

        if ($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] != 'SUCCESS')
        {
            Log::record($wxOrder,'error');
            Log::record('获取预支付订单失败','error');
        }
        // 保存prepay_id
        $this->recodPreOrder($wxOrder);
        $signature = $this->sign(); //
        return $signature;

    }

    private function sign ($wxOrder) {
        $jsApiPayDate = new \WxPayJsApiPay();
        $jsApiPayDate->SetAppid(config('wx.app_id'));
        $jsApiPayDate->SetTimeStamp((string)time());
        $rand = md5(time().mt_rand(0,1000));
        $jsApiPayDate->SetNonceStr($rand);
        $jsApiPayDate->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayDate->SetSignType('md5');

        $sign = $jsApiPayDate->MakeSign(); // 签名
        $rawDate = $jsApiPayDate->GetValues(); // 将对象转数组 内置方法
        $rawDate['paySign'] = $sign;
        unset($rawDate['appId']);

        return $rawDate;  // 返回给小程序
    }

    private function recodPreOrder ($wxOrder) {
        OrderModel::where(['id'=>$this->orderID])->update(['prepay_id'=>$wxOrder['prepay_id']]);
    }


    private function checkOrderValid () {
        $order = OrderModel::where(['id'=>$this->orderID])->find();

        if (!$order) {
            throw new OrderException();
        }
        if (!Token::isValidOperate($order->user_id)) {
            throw new TokenException([
                'msg'=> '订单与用户不匹配',
                'errorCode' => 10003,
            ]);
        }
        if ($order->status !=OrderStatusEnum::UNPAID) {
            throw new OrderException([
                'msg' => '订单已支付过啦',
                'errorCode' => 80003,
                'code' => 400
            ]);
        }
        $this->orderNO = $order->order_no;
        return true;
    }

}