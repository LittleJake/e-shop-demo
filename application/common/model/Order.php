<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 22:49
 */

namespace app\common\model;


class Order extends BaseModel
{
    public function OrderGoods(){
        return $this->hasMany('OrderGoods', 'order_id', 'id');
    }

    public function Account(){
        return $this->hasOne('Account', 'id', 'user_id');
    }

    public function Shipping(){
        return $this->hasOne('Shipping', 'id', 'shipping_type');
    }

    public function getOrderCount($where = [],$field ='*'){
        return parent::getCount($where,$field);
    }
}