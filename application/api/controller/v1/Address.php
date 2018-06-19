<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/4
 * Time: 14:00
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\User as UserModel;
use app\api\service\Token as TokenService;
use app\api\validate\AddressNew;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;

class Address extends BaseController
{

    // TP5 前置操作，
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress'],
    ];


    public function createOrUpdateAddress ()
    {
        $valadate = new AddressNew();
        $valadate->goCheck();
        /*根据token 获取uid
        根据uid来查找用户数据，判断用户是否存在，不存在，跑出异常
        获取用户从客户端传递过来的地址信息
        更具用户信息是否存在，从而判断十天假还是更新地址*/
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if (!$user) {
            throw new UserException();
        }

        $dataArray = $valadate->getDateByRule(input('post.'));
        $userAdderss = $user->address; // 拿到地址
        if (!$userAdderss) {
            $user->address()->save($dataArray);
        } else {
            $user->address->save($dataArray);
        }
        return json(new SuccessMessage(),201);
    }

}