<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/4/5
 * Time: 1:22
 */

namespace app\index\validate;


use app\common\validate\Common;

class Rate extends Common
{
    protected $rule = [
        'star' => 'require',
        'comment' => 'require',
    ];

    protected $message  =   [
        'star.require' => '参数错误',
        'comment.require' => '参数错误',
    ];
}