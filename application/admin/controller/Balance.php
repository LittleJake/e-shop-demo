<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/26
 * Time: 23:51
 */

namespace app\admin\controller;


use app\common\model\BalanceChange;

class Balance extends Base
{
    /** 用户余额 */
    public function indexAction(){
        return $this->fetch();
    }

    public function balancelistAction(){
        $modelBalance = new \app\common\model\Balance();
        $query = $modelBalance->with('Account') -> select();


        return json([
            'code' => 0,
            'msg' => '',
            'count' => $modelBalance->getBalanceCount(),
            'data' => $query
        ]);
    }

    /** 用户流水（嵌套在用户余额列表） */
    public function changeAction(){
        $uid = input('get.uid');

        $this->assign('uid', $uid);
        return $this->fetch();
    }

    public function changelistAction($uid = 0){
        $modelBalanceChange = new BalanceChange();
        $query = $modelBalanceChange -> with('Account') -> where([
            'user_id' => $uid
        ]) -> select();


        return json([
            'code' => 0,
            'msg' => '',
            'count' => $modelBalanceChange->getBalanceChangeCount(['user_id' => $uid]),
            'data' => $query
        ]);
    }
}