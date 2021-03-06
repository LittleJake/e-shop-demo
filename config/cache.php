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

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 驱动方式
    'type'   => 'Redis',
    // 缓存前缀
    'prefix' => '',
    // 缓存有效期 0表示永久缓存
    'expire' => 0,
    //Redis host
    'host'       => '127.0.0.1',
    //Redis port
    'port'       => 6379,
    //Redis 连接密码
    'password'   => '',
    //Redis数据库选择
    'select'     => 0,
    //连接超时时间
    'timeout'    => 0,
    //持久化连接
    'persistent' => false,
    //数据序列化
    'serialize'  => true,
];
