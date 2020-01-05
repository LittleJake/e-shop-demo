<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/2/2
 * Time: 21:21
 */

namespace app\admin\controller;


use app\common\model\Account;

class User extends Base
{
    public function userlistAction(){
        $modelAccount = new Account();
        $query = $modelAccount ->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $modelAccount->getAccountCount(),
            'data' => $query
        ]);
    }

    public function indexAction(){
        return $this->fetch();
    }

    public function adminAction(){
        return $this->fetch();
    }
}