<?php
/**
 * Created by PhpStorm.
 * User: Imoox
 * Email: 1430236133@qq.com
 * Date: 2018/6/1
 * Time: 12:27
 */

namespace app\api\controller\v1;
use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\api\validate\IDMustBePostivenInt;
use app\lib\exception\ThemeException;

/**
 * Class Theme
 * @package app\api\controller\v1
 * 主题
 */
class Theme
{

    /**
     * @url /theme?ids=id1,id2,id3,...
     * @return 一组theme模型
     */
    public function getSimpleList ($ids = '') {
        (new IDCollection())->goCheck();
        $ArrIds = explode(',',$ids);
        $result = ThemeModel::getThemeByIds($ArrIds);
        if (!$result) {
            throw new ThemeException();
        }
        return $result;
    }

    /**
     * @url /theme/:id
     */
    public function getComplexOne ($id) {
        // 参数检验
        (new IDMustBePostivenInt())->goCheck();

        $result = ThemeModel::getThemeWithProducts($id);
        if (!$result) {
            throw new ThemeException();
        }

        return $result;

    }
}