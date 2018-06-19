<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/5
 * Time: 13:26
 */

namespace app\api\controller;


use think\Controller;
use app\api\service\Token as TokenService;

class BaseController extends Controller
{

    protected function checkPrimaryScope () {
        TokenService::checkPrimaryScope();
    }

    protected function checkExclusiveScope () {
        TokenService::checkExclusiveScope();
    }



}