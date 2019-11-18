<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 23:03
 */

namespace app\admin\model;


use app\common\model\BaseModel;

class AdminMenu extends BaseModel
{
    public function AdminAccount(){
        return $this->belongsToMany('AdminAccount', 'admin_privilege', 'admin_id', 'menu_id');
    }
}