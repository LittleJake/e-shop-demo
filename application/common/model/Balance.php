<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 22:50
 */

namespace app\common\model;


class Balance extends BaseModel
{
    public function Account(){
        return $this->belongsTo('Account', 'id', 'user_id');
    }
}