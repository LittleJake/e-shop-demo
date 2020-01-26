<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/19
 * Time: 3:01
 */

namespace app\admin\controller;


class Shipping extends Base
{
    /** 发货页面 */
    public function indexAction(){
        return $this->fetch();
    }

    public function shippinglistAction(){
        return json();
    }

    public function shipAction(){

    }

    /** 物流模板 */
    public function methodAction(){
        return $this->fetch();
    }

    public function methodlistAction(){
        return json();
    }

    public function addMethodAction(){
        return $this->fetch();
    }

    public function editMethodAction(){
        return $this->fetch();
    }

    public function delMethodAction(){
        return $this->fetch();
    }

}