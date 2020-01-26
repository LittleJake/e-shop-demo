<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/27
 * Time: 0:30
 */

namespace app\admin\controller;


class Track extends Base
{
    /** 溯源码追踪 */
    public function indexAction(){
        return $this->fetch();
    }

    public function addAction(){
        return $this->fetch();
    }

    public function tracklistAction(){
        return json();
    }
}