<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/2/6
 * Time: 13:29
 */

namespace app\admin\controller;


class Log extends Base
{
    /** 操作记录 */
    public function indexAction(){
        return $this->fetch();
    }

    public function logListAction(){
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

    public function delAction(){

    }
}