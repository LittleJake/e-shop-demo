<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/26
 * Time: 23:51
 */

namespace app\admin\controller;


use app\common\library\Enumcode\LayuiJsonCode;
use app\common\model\BalanceChange;

class Balance extends Base
{
    /** 用户余额 */
    public function indexAction(){
        return $this->fetch();
    }

    public function balanceListAction(){
        $balance = model('Balance');
        $query = $balance->p()->with('Account') -> select();


        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $balance->getBalanceCount(),
            'data' => $query
        ]);
    }

    /** 用户流水（嵌套在用户余额列表） */
    public function changeAction($uid = 0){
        $this->assign('uid', $uid);
        return $this->fetch();
    }

    public function changeListAction($uid = 0){
        $modelBalanceChange = new BalanceChange();
        $where[] = ['user_id', '=', $uid];
        $query = $modelBalanceChange ->p() -> with('Account') -> where($where) -> select();


        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $modelBalanceChange->getBalanceChangeCount($where),
            'data' => $query
        ]);
    }
}