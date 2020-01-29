<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/25
 * Time: 1:33
 */

namespace app\common\validate;

use think\Validate;

class Common extends Validate
{

    protected function isPositiveInteger($value,$rule='',$data='', $field=''){
        if(is_numeric($value) && is_int($value+0) && ($value+0)>0)
            return true;
        else
            return false;
    }

    protected function isPositiveDouble($value,$rule='',$data='', $field=''){
        if(is_numeric($value) && is_float($value+0.01) && ($value+0)>0)
            return true;
        else
            return false;
    }
}