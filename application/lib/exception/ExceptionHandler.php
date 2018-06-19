<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/5/31
 * Time: 13:44
 */

namespace app\lib\exception;


use Exception;
use think\Config;
use think\exception\Handle;
use think\Log;
use think\Request;

class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;
    public function render(Exception $e)
    {

        if ($e instanceof BaseException) {
            // 如果是自定义异常
            $this->code         = $e->code;
            $this->msg          = $e->msg;
            $this->errorCode    = $e->errorCode;
        } else {
            if ( /*config('app_debug')*/ Config::get('app_debug') ) {
                return parent::render( $e );
            }
            $this->msg          = '内部错误';
            $this->code         = 500;
            $this->errorCode    = 999;
            $this->recordErrorLog($e);
        }

        // 获取当前请求url 地址
        $request = Request::instance();
        $result = [
            'msg'           => $this->msg,
            'error_code'    => $this->errorCode,
            'request_url'   => $request->url(),
        ];

        return json( $result , $this->code );

    }

    public function recordErrorLog ( Exception $e) {
        // 初始化
        Log::init([
            'type'  => 'file',
            'path'  =>LOG_PATH,
            'level' => ['error'],
        ]);
        Log::record($e->getMessage(),'error');
    }

}