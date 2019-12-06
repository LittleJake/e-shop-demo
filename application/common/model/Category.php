<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 22:53
 */

namespace app\common\model;


class Category extends BaseModel
{
    public function Good(){
        return $this->hasMany('Good','cate_id', 'id');
    }
}