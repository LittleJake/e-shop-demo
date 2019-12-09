<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 22:51
 */

namespace app\common\model;


class Good extends BaseModel
{
    public function GoodCat(){
        return $this->hasMany('GoodCat', 'good_id', 'id');
    }

    public function Rate(){
        return $this->hasMany('Rate', 'good_id', 'id');
    }
}