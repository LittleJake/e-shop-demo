<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 12:52
 */

namespace app\index\controller;


use think\Controller;

class Common extends Controller
{
    public function __construct(){
        parent::__construct();

        //分页个数
        !defined('PAGE')&&define('PAGE', 4);
        $this->assign('is_login', $this ->isLogin());
    }

    function isLogin() {
        //登录判断
        if(session('?user') && session('?user_id'))
            return true;

        return false;
    }
}