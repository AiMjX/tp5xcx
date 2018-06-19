<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/3
 * Time: 14:43
 */

namespace app\api\controller\v1;


use app\api\service\UserToken;
use app\api\validate\TokenGet;

class Token
{
    /**
     * @param string $code
     * @throws \app\lib\exception\ParameterException
     * @url api/:version/token/user
     */
    public function getToken ($code = '')
    {
        (new TokenGet())->goCheck();

        $ut = new UserToken($code);

        $token = $ut->get();

        return [ 'token' => $token ];
    }

}