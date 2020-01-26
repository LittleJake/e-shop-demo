<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/26
 * Time: 23:51
 */

namespace app\admin\controller;


class Balance extends Base
{
    /** 用户余额 */
    public function indexAction(){
        return $this->fetch();
    }

    public function balancelistAction(){
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $modelGood->getGoodCount(),
            'data' => $query
        ]);
    }

    /** 用户流水（嵌套在用户余额列表） */
    public function changeAction(){
        $uid = input('get.uid');

        return $this->fetch();
    }

    public function changelistAction(){
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $modelGood->getGoodCount(),
            'data' => $query
        ]);
    }
}