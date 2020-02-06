<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/27
 * Time: 17:19
 */

namespace app\common\model;


class Track extends BaseModel
{
    public function getTrackCount($where = [],$field ='*'){
        return parent::getCount($where, $field);
    }
}