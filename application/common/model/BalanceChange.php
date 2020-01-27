<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 22:50
 */

namespace app\common\model;


class BalanceChange extends BaseModel
{

    public function Account(){
        return $this->belongsTo('Account', 'user_id', 'id');
    }

    public function getBalanceChangeCount($where = [],$field ='*')
    {
        return parent::getCount($where,$field);
    }
}