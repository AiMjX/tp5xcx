<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/5/31
 * Time: 11:52
 */

namespace app\api\validate;


use app\lib\exception\ParameterException;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{

    public function goCheck () {

        $resquest = Request::instance();
        $params = $resquest->param();
        //dump($params);exit();
        $result = $this->batch()->check($params);
        if (!$result) {
            throw new ParameterException([
                'msg' => $this->error //$this->getError()
            ]);
        } else {
            return true;
        }

    }

    /** 自定义验证
     * @param $value 需要验证的字段的值
     * @param string $rule 验证规则传递过来的值
     * @param string $data
     * @param string $field 需要验证的字段名
     * @return bool|string
     */
    protected function isPositiveInteger ( $value )
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        } else {
            return false;
            //return $field.'必须为正整数';
        }

    }

    public function getDateByRule ($arrays) {
        if (array_key_exists('user_id',$arrays) | array_key_exists('uid',$arrays)) {
            // 不允许包含user_id或者uid，防止恶意覆盖user_id外键
            throw new ParameterException(['msg' => '参数中包含有非法的参数名user_id或者uid']);
        }
        $newArray = [];
        foreach ($this->rule as $key => $value) {
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;
    }

    /*protected function isNotEmpty ( $value )
    {
        if (empty($value)) {
            return false;
        } else {
            return true;
            //return $field.'必须为正整数';
        }
    }*/

    protected function isNotEmpty($value, $rule='', $data='', $field='')
    {
        if (empty($value)) {
            return $field . '不允许为空';
        } else {
            return true;
        }
    }

    //没有使用TP的正则验证，集中在一处方便以后修改
    //不推荐使用正则，因为复用性太差
    //手机号的验证规则
    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }



}