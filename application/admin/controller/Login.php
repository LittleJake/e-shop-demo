<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jake
 * Date: 12/4/2019
 * Time: 8:45 PM
 *
 * Stay simple, stay naive.
 *
 */

namespace app\admin\controller;


class Login extends Common
{
    public function loginAction(){

        return $this -> fetch();
    }
    public function forgetAction(){

        return $this -> fetch();
    }

    public function vercodeAction(){

        return $this -> fetch();
    }
}