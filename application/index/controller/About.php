<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/12/27
 * Time: 23:34
 */

namespace app\index\controller;


class About extends Common
{
    public function indexAction(){
        $this->assign('page_title', '关于我们');
        return $this->fetch();
    }

    public function certAction(){
        $this->assign('page_title', '资质');
        return $this->fetch();
    }
}