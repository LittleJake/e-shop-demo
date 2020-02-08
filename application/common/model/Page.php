<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/2/8
 * Time: 23:29
 */

namespace app\common\model;


class Page extends BaseModel
{
    public function getPageCount($where = [],$field ='*'){
        return parent::getCount($where,$field);
    }
}