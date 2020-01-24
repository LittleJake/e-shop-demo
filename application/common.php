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

function secret($s){
    return password_hash($s . 'salt',PASSWORD_BCRYPT);
}


function check_secret($s, $password){
    return password_verify($s . 'salt', $password);
}


function random_str($num = 8, $type = 'str'){
    $rnt = '';

    if($type == 'str')
        $str = 'abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ1234567890';
    else
        $str = '123456790';

    $len = strlen($str);

    for($i =0;$i< $num;$i++)
        $rnt .= $str[rand(0,$len-1)];

    return $rnt;
}