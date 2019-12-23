<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/2/2
 * Time: 21:25
 */

namespace app\admin\controller;

use think\App;

class Base extends Common
{
    function __construct(App $app = null)
    {
        parent::__construct($app);

        if(!$this->isLogin())
            return $this->redirect(('admin/login/login'));
    }

}