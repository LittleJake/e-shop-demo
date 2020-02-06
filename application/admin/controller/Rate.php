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
        return $this->fetch();
    }

    public function rateListAction(){
//        $where = [];
//
//        !empty(input('id')) && $where[] = ['id', '=', input('id')];
//        !empty(input('title')) && $where[] = ['title', 'like', '%'.input('title').'%'];
//        (input('status') != '') && $where[] = ['status', '=',input('status')];

        $rate = model('Rate');
        $query = $rate->p()->with('Account')->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $rate->getRateCount(),
            'data' => $query
        ]);
    }

    public function delAction(){
        return $this->fetch();
    }
}