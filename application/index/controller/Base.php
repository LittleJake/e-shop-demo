<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/2/2
 * Time: 21:27
 */

namespace app\index\controller;


use think\Controller;

class Base extends Controller
{

    function isLogin() {
        if(session('?user') && session('?token'))
            return true;

        return false;
    }
}