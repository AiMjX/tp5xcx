<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/1
 * Time: 12:45
 */

namespace app\api\validate;


class IDCollection extends BaseValidate
{
    protected $rule = [
        'ids' => 'require|checkIDs',
    ];

    protected $message = [
        'ids.require' => 'ids不能为空',
        'ids.checkIDs' => 'ids必须是以英文状态下逗号分割的正整数',
        ];

    protected function checkIDs ($value) {
        $values = explode(',', $value);
        if (empty($values)) {
            return false;
        }
        foreach ($values as $k=>$val) {
            if (!self::isPositiveInteger($val)) {
                // 必须是正整数
                return false;
            }
        }
        return true;
    }

}