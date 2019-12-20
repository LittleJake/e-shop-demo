<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/12/20
 * Time: 14:18
 */

namespace app\index\controller;


use app\common\model\Account;

class Balance extends Base
{

    public function indexAction(){

        $modelAccount = new Account();
        $modelAccount -> where([
            'id' => session('user_id')
        ])->with('BalanceChange')->find();

        $change = $modelAccount->BalanceChange()-> order('update_time','desc')->paginate(10);

        $this->assign('change', $change);

        $this->assign('page_title', '余额明细');
        return $this->fetch();
    }

    public function chargeAction(){



        $this->assign('page_title', '余额充值');
        return $this->fetch();
    }
}