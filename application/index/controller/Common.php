<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 12:52
 */

namespace app\index\controller;


use app\common\library\Enumcode\PageStatus;
use think\Controller;

class Common extends Controller
{
    public function __construct(){
        parent::__construct();

        //分页个数
        !defined('PAGE')&&define('PAGE', 5);


        $this->assign('is_login', $this ->isLogin());

        //网站设置
        $set = model('AdminSetting');
        $query = $set ->cache(true, 600)-> select();
        $option = [];
        foreach ($query as $v)
            $option[$v['keyword']] = $v['content'];
        $this->assign('option', $option);

        //网站独立页面
        $page = model('Page');
        $query = $page->cache(true, 600)
            ->where('status', PageStatus::PAGE_SHOW)->select();
        $this->assign('PAGE', $query);
    }

    function isLogin() {
        //登录判断
        if(session('?user') && session('?user_id'))
            return true;

        return false;
    }
}