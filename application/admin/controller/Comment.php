<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/19
 * Time: 3:00
 */

namespace app\admin\controller;


class Comment extends Base
{
    public function indexAction(){
        return $this->fetch();
    }
}