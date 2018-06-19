<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/7
 * Time: 15:42
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePostivenInt;
use app\api\service\Pay as PayService;

class Pay extends BaseController
{
    // 支付只能 用户能访问，管理员不能访问
    protected $beforeActionList = array(
        'checkExclusiveScope' => array('only'=> 'getPreOrder')
    );

    /**
     * 请求预订单信息，api到微信服务器上再生成一个 服务器所要求的订单
     */
    public function getPreOrder ($id='')
    {

        (new IDMustBePostivenInt())->goCheck();

        $pay = new PayService($id);

        return $pay->pay();
    }

    public function receiveNotify () {
        //  检测库存量，避免超卖。
        //  更新订单状态 order表下的status 状态
        //  减库存
        // 如果成功---返回微信成功处理消息   如果失败----返回不成功结果给微信服务器

        // 微信访问方式：post    xml格式：不携带参数

        $notify = new WxNotify();
        $notify->Handle();



    }

}