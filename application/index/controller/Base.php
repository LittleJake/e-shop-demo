<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/2/2
 * Time: 21:27
 */

namespace app\index\controller;


class Base extends Common
{

    public function __construct(){
        parent::__construct();

        if(!$this->isLogin())
            return $this->redirect('index/login/login', ['r' =>  urlencode($this->request->url(true))]);
    }

}