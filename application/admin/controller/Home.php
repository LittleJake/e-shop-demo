<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/9
 * Time: 23:02
 */

namespace app\admin\controller;


class Home extends Base
{
    public function consoleAction(){
        return $this->fetch();
    }
}