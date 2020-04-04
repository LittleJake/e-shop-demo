<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/25
 * Time: 1:28
 */

namespace app\index\validate;

use app\common\validate\Common;

class Order extends Common
{
    protected $rule = [
        'cat' => 'require',
        'num' => 'require|isGoodNum',
        'pay' => 'require|isPositiveInteger',
        'add_id' => 'require|isPositiveInteger',
        'ship' => 'require|isPositiveInteger',
    ];

    protected $message  =   [
        'cat.require' => '物品不存在',
        'add_id.require' => '地址不存在',
        'add_id.isPositiveInteger' => '地址不存在',
        'num.require' => '物品数量错误',
        'num.isGoodNum' => '物品数量错误',
        'pay.require' => '付款方式不存在',
        'pay.isPositiveInteger' => '付款方式不存在',
        'ship.require' => '邮寄方式不存在',
        'ship.isPositiveInteger' => '邮寄方式不存在',
    ];

    protected $scene = [
        'checkout' => ['cat', 'num']
    ];
}