<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/28
 * Time: 15:51
 */

namespace app\index\validate;


use app\common\validate\Common;

class Cart extends Common
{
    protected $rule = [
        'cat_id' => 'require|isPositiveInteger',
        'num' => 'require|isPositiveInteger'
    ];

}