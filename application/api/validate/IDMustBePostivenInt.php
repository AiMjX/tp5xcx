<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/5/31
 * Time: 11:51
 */

namespace app\api\validate;


class IDMustBePostivenInt extends BaseValidate
{

    protected $rule = [
        'id' => 'require|isPositiveInteger',
    ];
    protected $message = [
        'id.require' => 'id不能为空',
        'id.isPositiveInteger' => 'id必须为正整数',
    ];








}