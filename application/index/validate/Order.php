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
        'num' => 'require',
        'pay' => 'require|isPositiveInteger',
        'add_id' => 'require|isPositiveInteger',
        'total' => 'require|isPositiveInteger',
        'ship' => 'require|isPositiveInteger',
    ];

    protected $message  =   [
        'cat.require' => '物品不存在',
        'add_id.require' => '地址不存在',
        'num.require' => '请填写物品数量',
        'pay.require' => '付款方式不存在',
        'total.require' => '价格不存在',
        'ship.require' => '邮寄方式不存在',
    ];
}