<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/27
 * Time: 16:05
 */

namespace app\admin\controller;


class Set extends Base
{
    /** 设置 */
    public function indexAction(){
        $set = model('AdminSetting');
        if($this->request->isPost()){


        }

        $query = $set -> select();
        $option = [];
        foreach ($query as $v)
            $option[$v['keyword']] = $v['content'];

        $this->assign('option', $option);
        return $this->fetch();
    }
    public function userAction(){
        $admin = model('AdminAccount');
        $query = $admin->get($this->adminid());

        $this->assign('admin', $query);
        return $this->fetch();
    }
    public function passwdAction(){
        return $this->fetch();
    }
}