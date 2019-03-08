<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/2/2
 * Time: 21:27
 */

namespace app\index\controller;


use think\Controller;

class Base extends Controller
{
	public function __construct(){
		parent::__construct();
		
		define('PAGE', 4);
		$this->assign('is_login', $this ->isLogin());
	}


    function isLogin() {
        if(session('?user') && session('?user_id'))
            return true;

        return false;
    }
	
	
}