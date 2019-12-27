<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 22:54
 */

namespace app\common\model;


class AdminAccount extends BaseModel
{

    public function AdminLog(){
        return $this->hasMany('AdminLog','admin_id','id');
    }

    public function AdminRole(){
        return $this->hasOne('AdminRole','id','role_id');
    }


}