<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/5/31
 * Time: 14:56
 */

namespace app\lib\exception;


class ParameterException extends BaseException
{
    public $code = 400;
    public $msg = '参数错误';
    public $errorCode = 10000;
}