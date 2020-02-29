<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/27
 * Time: 16:05
 */

namespace app\admin\controller;


use app\common\library\Enumcode\LayuiJsonCode;

class Set extends Base
{
    /** 设置 */
    public function indexAction(){
        $set = model('AdminSetting');
        if($this->request->isPost()){
            $data = $this->request->post();
            foreach ($data as $k => $v){
                $set -> update(['content' => $v],['keyword' => $k]);
            }

            $this->log("修改网站设置");
            return json(['code'=> 1, 'msg' => '修改成功']);
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
        if($this->request->isPost()){
            $data = $this->request->post();

            $admin->update([
                'username'=>$data['username'],
                'email'=>$data['email'],
            ],['id' => $this->adminid()]);

            $this->log("修改个人信息");
            return json(['code'=> 1, 'msg' => '修改成功']);
        }

        $query = $admin->get($this->adminid());

        $this->assign('admin', $query);
        return $this->fetch();
    }
    public function passwdAction(){
        if($this->request->isPost()){
            $account = model('AdminAccount');
            $data = $this->request->post();
            $valid = validate('User');
            if(!$valid->scene('change')->check($data))
                return json(['code'=> 0, 'msg'=> $valid->getError()]);

            $query = $account->get($this->adminid());

            if(!check_secret($data['old_password'], $query['password']))
                return json(['code'=> 0, 'msg'=> '原密码错误']);

            $account->update(['password'=>secret($data['password'])],['id'=> $this->adminid()]);

            $this->log("修改密码");
            return json(['code'=> 1, 'msg' => '修改成功']);
        }

        return $this->fetch();
    }
}