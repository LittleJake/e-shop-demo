<?php

use think\facade\Route;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//index
//index.get
Route::get('good/info/[:id]', 'index/good/good');
Route::get('shop/info/[:id]', 'index/shop/shopInfo');
Route::get('logout', 'index/login/logout');
Route::get('user/order', 'index/user/order');
Route::get('user/balance', 'index/balance/index');
Route::get('user/charge', 'index/balance/charge');
Route::get('category/list', 'index/category/index');


//index.any
Route::any('user/address/add','index/user/addAddress');
Route::any('login', 'index/login/login');
Route::any('user/address','index/user/address');
Route::any('register','index/login/reg');

//index.post
Route::post('good/order','index/good/order');
Route::post('good/checkout','index/good/checkout');



//admin
//admin.get
Route::get('admin/login','admin/login/login');
Route::get('admin/forget','admin/login/forget');
Route::get('admin/ajaxlogin','admin/login/ajaxLogin');

