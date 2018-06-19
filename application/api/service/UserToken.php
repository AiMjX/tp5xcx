<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/3
 * Time: 14:52
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use app\api\model\User as UserModel;

class UserToken extends Token
{
    protected $code;

    protected $wxAppID;

    protected $wxAppSecret;

    protected $wxLoginUrl;

    function __construct( $code )
    {
        $this->code         = $code;

        $this->wxAppID      = config('wx.app_id');

        $this->wxAppSecret  = config('wx.app_secret');

        $this->wxLoginUrl   = sprintf(config('wx.login_url'),
            $this->wxAppID,$this->wxAppSecret,$this->code);

    }

    public function get()
    {
        $wxResult = http_curl($this->wxLoginUrl,'','json','');
        //$wxResult = json_decode($res,true);
        if (empty($wxResult))
        {

            throw new Exception('获取session_key及openID时异常，微信内部错误');

        } else{

            $loginFail = array_key_exists('errorcode',$wxResult);
            if ($loginFail) {

                self::processLoginError($wxResult);

            } else{

                return self::grantToken($wxResult);

            }
        }
    }

    private function grantToken ($wxResult) {
        // 拿到openid
        // 数据库看下，这个openid是否已存在
        // 如果存在不处理，如果不存在新增一条user 记录
        // 生成令牌，准备缓存数据，写入缓存
        // 把令牌返回到客户端去
        // key:令牌
        // value:wxResult,uid,scope
        $openID = $wxResult['openid'];
        $user = UserModel::getByOpenID($openID);
        if ($user) {
            $uid = $user->id;
        }else {
            $uid = self::newUser($openID);
        }
        $cachedValue = self::prepareCachedValue($wxResult,$uid);
        $token = $this->saveToCache($cachedValue);
        return $token;

    }

    // 写入缓存
    private function saveToCache ($cachedValue) {
        $key = self::generateToken();
        $value = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');
        // tp5 的缓存
        $request = cache($key,$value,$expire_in);

        if (!$request) {
            throw new TokenException([
                'mag' => '服务器缓存异常',
                'errorCode' => 10005,
            ]);
        }

        return $key;
    }

    // 拼装数据
    private function prepareCachedValue ($wxResult,$uid) {
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        $cachedValue['scope'] = ScopeEnum::User;
        return $cachedValue;
    }

    // 像数据库插入一条数据 用户
    private function newUser ($openID)
    {
        $user = UserModel::create(['openid'=>$openID]);
        return $user->id;
    }

    //
    private function processLoginError ($wxResult)
    {
        throw new WeChatException([
            'msg' =>$wxResult['errmsg'],
            'errorCode' => $wxResult['errorcode'],
        ]);
    }



}