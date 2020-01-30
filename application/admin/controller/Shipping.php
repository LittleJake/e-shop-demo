<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/19
 * Time: 3:01
 */

namespace app\admin\controller;


class Shipping extends Base
{


    /** 物流模板 */
    public function indexAction(){
        return $this->fetch();
    }

    public function shippinglistAction(){
        $modelShipping = new \app\common\model\Shipping();
        $query = $modelShipping ->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $modelShipping->getShippingCount(),
            'data' => $query
        ]);
    }

    public function addAction(){
        return $this->fetch();
    }

    public function editAction(){
        return $this->fetch();
    }

    public function delAction(){
        return $this->fetch();
    }

}