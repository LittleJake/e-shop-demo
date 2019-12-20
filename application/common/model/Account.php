<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 22:50
 */

namespace app\common\model;


class Account extends BaseModel
{
    public function Balance(){
        return $this->hasOne('Balance','user_id','id');
    }
    public function BalanceChange(){
        return $this->hasMany('BalanceChange','user_id','id');
    }
}