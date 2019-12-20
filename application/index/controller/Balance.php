<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/12/20
 * Time: 14:18
 */

namespace app\index\controller;


class Balance extends Base
{

    public function indexAction(){



        $this->assign('page_title', '余额明细');
        return $this->fetch();
    }

    public function chargeAction(){



        $this->assign('page_title', '余额充值');
        return $this->fetch();
    }
}