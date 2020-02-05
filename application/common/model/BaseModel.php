<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 22:47
 */

namespace app\common\model;


use think\Model;
use think\Db;

class BaseModel extends Model
{
    public function p(){
        $limit = input('limit', 10);
        $page = input('page', 1);

        return $this->limit(($page-1) * $limit, $limit);
    }

    final protected function getCount($where = [],$field ='*'){
        return Db::name($this->name)->where($where)->count($field);
    }
}