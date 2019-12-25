<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/2/2
 * Time: 21:21
 */

namespace app\admin\controller;


class User extends Base
{
    public function listAction(){
        return $this->fetch();
    }

    public function indexAction(){
        return $this->fetch();
    }

    public function adminAction(){
        return $this->fetch();
    }
}