<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/19
 * Time: 3:02
 */

namespace app\admin\controller;


class Rate extends Base
{
    /** 评价管理（嵌套在商品列表） */
    public function indexAction(){
        return $this->fetch();
    }

    public function ratelistAction(){
        return json();
    }

    public function delAction(){
        return $this->fetch();
    }
}