<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/12/7
 * Time: 1:01
 */

namespace app\common\model;


class Cart extends BaseModel
{
    public function GoodCat(){
        return $this->hasOne('GoodCat', 'id', 'cat_id');
    }
}