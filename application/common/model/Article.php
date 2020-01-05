<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/12/27
 * Time: 23:05
 */

namespace app\common\model;


class Article extends BaseModel
{
    public function AdminAccount(){
        return $this->hasOne('AdminAccount', 'id', 'admin_id');
    }

    public function getArticleCount($where = [],$field ='*'){
        return parent::getCount($where, $field);
    }
}