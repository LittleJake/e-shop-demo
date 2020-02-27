<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 23:02
 */

namespace app\common\model;


class AdminLog extends BaseModel
{
    public function AdminAccount(){
        return $this->belongsTo('AdminAccount','admin_id','id');
    }

    public function getLogCount($where = [],$field ='*'){
        return parent::getCount($where, $field);
    }
}