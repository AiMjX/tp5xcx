<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/3
 * Time: 19:23
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    public static function generateToken () {
        // 32个字符组成随机字符串
        $randChars = getRandChar(32);
        // 用三组字符串，进行MD5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        // 盐
        $salt = config('secure.token_salt');

        return md5($randChars.$timestamp.$salt);
    }

    public static function getCurrentTokenVar ($key) {
        $token = Request::instance()->header('token'); // 客户端通过header头传递token令牌过来
        $vars = Cache::get($token);  // 通过token 令牌去缓存里换取相应信息
        if (!$vars) {  // 判断缓存是否失效 或者别的问题
            throw new TokenException();
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars,true);
            }
            if (array_key_exists($key,$vars)) {
                return $vars[$key];
            } else {
                throw new Exception('尝试获取的token变量并不存在');
            }
        }

    }

    public static function getCurrentUid () {
        // token
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    //  管理员和用户都能访问
    public static function checkPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope >= ScopeEnum::User) {
                return true;
            }else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    // 只有用户才能访问
    public static function checkExclusiveScope () {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope == ScopeEnum::User) {
                return true;
            }else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    public static function isValidOperate ($checkUid)
    {
        if (!$checkUid) {
            throw new Exception('检查Uid时必须传入一个被检测的Uid');
        }
        $currentOperateUid = self::getCurrentUid();
        if ( $currentOperateUid == $checkUid) {
            return true;
        }
        return false;
    }

}