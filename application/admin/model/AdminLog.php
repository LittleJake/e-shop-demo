<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 23:02
 */

namespace app\admin\model;


use app\common\model\BaseModel;

class AdminLog extends BaseModel
{
    public function AdminAccount(){
        return $this->belongsTo('AdminAccount','id','admin_id');
    }
}