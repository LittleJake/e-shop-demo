<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/9
 * Time: 13:43
 */

namespace app\admin\controller;


class Category extends Base
{
    public function indexAction(){
        return $this->fetch();
    }

    public function categoryAddAction(){
        return $this->fetch();
    }

    public function categoryEditAction(){
        return $this->fetch();
    }
}