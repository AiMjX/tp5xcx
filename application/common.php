<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * @param $url  请求地址
 * @param string $type  默认为get 请求 传递别的就是别的请求
 * @param string $res 返回数据类型  默认json
 * @param string $arr
 * @return mixed
 */
function http_curl($url, $type = 'get', $res = 'json', $arr = '')
{

    $ch = curl_init(); // 初始化
    //设置curl 参数
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($type == 'post') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
    }
    $result = curl_exec($ch); // 采集
    curl_close($ch); // 关闭
    if ($res == 'json') {
        return json_decode($result, true);
    }
}

function getRandChar ( $length ) {
    $str = null;
    $strPol = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $max = strlen($strPol ) - 1;
    for ($i=0 ; $i<$length ; $i++)
    {
        $str .= $strPol[rand(0 , $max)];
    }
    return $str;
}
