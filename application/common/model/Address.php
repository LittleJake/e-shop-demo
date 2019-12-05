<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 22:49
 */

namespace app\common\model;


class Address extends BaseModel
{
    public function Account(){
        return $this->belongsTo('Account','id', 'user_id');
    }

    public function Order(){
        return $this->belongsToMany('Order', 'Order', 'address_id', 'id');
    }
}