<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/2/2
 * Time: 16:23
 */

namespace app\admin\controller;


class Page extends Base
{
    /** 独立页面管理 */
    public function indexAction(){
        return $this->fetch();
    }

    public function pagelistAction(){
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
    public function addAction(){
        if($this->request->isPost()){

        }

        return $this->fetch();
    }

    public function editAction(){
        if($this->request->isPost()){

        }

        return $this->fetch();
    }

    public function delAction(){

    }

}