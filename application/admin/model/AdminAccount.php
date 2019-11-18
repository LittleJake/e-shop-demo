<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 22:54
 */

namespace app\admin\model;


use app\common\model\BaseModel;

class AdminAccount extends BaseModel
{
    public function AdminMenu(){
        return $this->belongsToMany('AdminMenu', 'admin_privilege', 'menu_id', 'admin_id');
    }
}