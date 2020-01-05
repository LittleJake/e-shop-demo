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
    final protected function getList($where = [], $field = true, $order = '', $paginate = 0)
    {

        empty($this->join) && !isset($where['status']) && $where['status'] = ['neq', -1];

        if (empty($this->join)) {

            !isset($where['status']) && $where['status'] = ['neq', -1];

            $query = $this;

        } else {

            $query = $this->join($this->join);
        }

        $query = $query->where($where)->order($order)->field($field);

        !empty($this->group) && $query->group($this->group);

        if (false === $paginate) {

            !empty($this->limit) && $query->limit((input('page') - 1) * input('limit'),input('limit'));

            $list = $query->select();

        } else {
            $list_rows = empty($paginate) || !$paginate ? 15 : $paginate;

            $list = $query->paginate(input('list_rows', $list_rows), false, ['query' => request()->param()]);
        }

        $this->join = []; $this->limit = []; $this->group = [];

        return $list;
    }

    final protected function getCount($where = [],$field ='*'){
        return Db::name($this->name)->where($where)->count($field);
    }
}