<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/1/31
 * Time: 22:14
 */

namespace app\admin\controller;


class Index extends Base
{
    public function indexAction()
    {
        return $this->fetch();
    }

    public function adminAction()
    {
        return $this->fetch();
    }

    public function homeAction()
    {
        return $this->fetch();
    }
}