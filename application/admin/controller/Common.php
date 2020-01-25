<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jake
 * Date: 12/4/2019
 * Time: 8:44 PM
 *
 * Stay simple, stay naive.
 *
 */

namespace app\admin\controller;


use think\Controller;

class Common extends Controller
{
    protected function isLogin() {
        if(session('?admin_user_name') && session('?admin_user_id'))
            return true;

        return false;
    }

    protected function adminid(){
        return session('admin_user_id');
    }
}