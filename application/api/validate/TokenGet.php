<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/3
 * Time: 14:44
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{

    protected $rule = [
        'code' => 'require|isNotEmpty'
    ];

    protected $message = [
        'code' => 'code不能为空是必填的',
    ];

}