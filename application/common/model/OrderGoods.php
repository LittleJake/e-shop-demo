<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 22:49
 */

namespace app\common\model;


class OrderGoods extends BaseModel
{
    public function Order(){
        return $this->belongsTo('Order', 'id', 'order_id');
    }
}