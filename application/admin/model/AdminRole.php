<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jake
 * Date: 12/5/2019
 * Time: 9:24 PM
 *
 * Stay simple, stay naive.
 *
 */

namespace app\admin\model;


use app\common\model\BaseModel;

class AdminRole extends BaseModel
{
    public function AdminAccount(){
        return $this->belongsTo('AdminAccount', 'role_id', 'id');
    }
}
