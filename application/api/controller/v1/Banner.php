<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/5/31
 * Time: 9:07
 */

namespace app\api\controller\v1;


use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePostivenInt;
use app\lib\exception\BannerMissException;
use think\Exception;

/**
 * Class Banner
 * @package app\api\controller\v1
 * 头部 轮播图
 */
class Banner
{

    /**
     * 获取指定id 的banner 信息
     * @param $id Banner 的id号
     * @param url /banner/:id
     * @param http GET
     * @throws \app\lib\exception\ParameterException
     */
    public function getBanner($id)
    {
        /*$timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        dump($timestamp);die();*/
        /*$wxResult = null;
        if (empty($wxResult))
        {
            throw new Exception('获取session_key及openID时异常，微信内部错误');
        }
        exit();*/
        //$a = 0.3;
        (new IDMustBePostivenInt())->goCheck($id);

        $banner = BannerModel::getBannerById($id);

        if (!$banner) {
            throw new BannerMissException();
        }

        return $banner;
    }


}