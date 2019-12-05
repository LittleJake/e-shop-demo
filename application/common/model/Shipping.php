<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 22:46
 */

namespace app\common\model;


class Shipping extends BaseModel
{
    public function Order(){
        return $this->belongsToMany('Order','Order','shipping_type','id');
    }
}