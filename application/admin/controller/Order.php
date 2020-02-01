<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/29
 * Time: 23:45
 */

namespace app\admin\controller;


class Order extends Base
{
    /** 订单管理 */
    public function indexAction(){
        return $this->fetch();
    }

    public function orderlistAction(){
        $modelOrder = new \app\common\model\Order();
        $query = $modelOrder -> with([
            'Account',
            'OrderGoods',
            'Shipping'
        ]) ->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $modelOrder->getOrderCount(),
            'data' => $query
        ]);
    }
    public function infoAction(){}
    public function changeAction(){}
    public function shipAction(){}

}