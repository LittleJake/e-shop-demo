<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/26
 * Time: 23:51
 */

namespace app\admin\controller;

use app\common\library\Enumcode\LayuiJsonCode;

class Balance extends Base
{
    /** 用户余额 */
    public function indexAction(){
        return $this->fetch();
    }

    public function balanceListAction(){
        $where = [];

        !empty(input('id')) && $where[] = ['id', '=', input('id')];
        !empty(input('username')) && $where[] = ['username', 'like', "%".input('username')."%"];

        $account = model('Account');
        $query = $account->p()->field('id,username')->with('Balance')->where($where) -> select();


        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $account->getAccountCount($where),
            'data' => $query
        ]);
    }

    /** 用户流水（嵌套在用户余额列表） */
    public function changeAction($uid = 0){
        $this->assign('uid', $uid);
        return $this->fetch();
    }

    public function changeListAction($uid = 0){
        $modelBalanceChange = model('BalanceChange');
        $where[] = ['user_id', '=', $uid];
        !empty(input('id'))&&$where[] = ['id', '=', input('id')];
        $query = $modelBalanceChange ->p() -> with('Account') -> where($where) ->order('id desc')-> select();

        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $modelBalanceChange->getBalanceChangeCount($where),
            'data' => $query
        ]);
    }
}