<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/8
 * Time: 13:35
 */

namespace app\api\validate;


class PagingParameter extends BaseValidate
{
    protected $rule = [
        'page' => 'isPositiveInteger',
        'size' => 'isPositiveInteger',
    ];

    protected $message = [
        'page'=> '分页参数必须正整数',
        'size' => '分页每页显示条数必须正整数'
    ];

}