<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/27
 * Time: 16:05
 */

namespace app\admin\controller;


class Set extends Base
{
    /** 设置 */
    public function indexAction(){
        return $this->fetch();
    }
    public function userAction(){
        return $this->fetch();
    }
    public function passwdAction(){
        return $this->fetch();
    }
}