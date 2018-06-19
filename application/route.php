<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/*return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];*/


use think\Route;

// banner
Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');
// theme 主题
Route::get('api/:version/theme', 'api/:version.Theme/getSimpleList');
// 点击theme 主题后跳转到的 产品列表   // 要修改config 文件 是否完整匹配路由
Route::get('api/:version/theme/:id', 'api/:version.Theme/getComplexOne');


Route::group('api/:version/product', function () {
    // 获取最近商品
    Route::get('/recent', 'api/:version.Product/getRecentProduct');
    // 获取分类 所对应的所有商品
    Route::get('/by_category', 'api/:version.Product/getAllProductInCategory');
    // 指定商品的详细信息
    Route::get('/:id', 'api/:version.Product/getOne', [], ['id' => '\d+']);
});



// 获取所有类别
Route::get('api/:version/category/all', 'api/:version.Category/getAllCategory');
//
Route::post('api/:version/token/user', 'api/:version.Token/getToken');



Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress');

Route::post('api/:version/order','api/:version.Order/placeOrder');
Route::get('api/:version/order/by_user','api/:version.Order/getSummaryByUser');
Route::get('api/:version/order/:id','api/:version.Order/getDetail',[],['id'=>'\d+']);

Route::post('api/:version/pay/pre_order','api/:version.Pay/getPreOrder');
Route::post('api/:version/pay/notify','api/:version.Pay/receiveNotify');
