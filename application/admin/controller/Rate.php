<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/19
 * Time: 3:02
 */

namespace app\admin\controller;


class Rate extends Base
{
    /** 评价管理（嵌套在商品列表） */
    public function indexAction(){

        $this->assign('good_id', input('good_id'));
        return $this->fetch();
    }

    public function rateListAction(){
        $where = [];

        !empty(input('good_id')) && $where[] = ['good_id', '=', input('good_id')];

        $rate = model('Rate');
        $query = $rate->p()->with('Account')->where($where)->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $rate->getRateCount($where),
            'data' => $query
        ]);
    }

    public function delAction(){
        return $this->fetch();
    }

}