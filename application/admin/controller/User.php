<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/2/2
 * Time: 21:21
 */

namespace app\admin\controller;

use app\common\library\Enumcode\LayuiJsonCode;
use app\common\library\Enumcode\UserStatus;

class User extends Base
{
    /** 用户操作 */
    public function userListAction(){
        $where = [];

        $where[] = ['status', 'in', '0,1'];
        !empty(input('id')) && $where[] = ['id', '=', input('id')];
        !empty(input('username')) && $where[] = ['username', 'like', "%".input('username')."%"];
        !empty(input('email')) && $where[] = ['email', 'like', "%".input('email')."%"];
        !empty(input('mobile')) && $where[] = ['mobile', '=', input('mobile')];

        $account = model('Account');
        $query = $account->p()->where($where)->select();
        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $account->getAccountCount($where),
            'data' => $query
        ]);
    }

    public function userAction(){
        return $this->fetch();
    }

    public function userEditAction($id = 0){
        $account = model('Account');

        if($this->request->isPost()){
            $data = $this->request->post('u');

            $account->update($data,['id' => $data['id']])
            && $this->log("修改用户信息，ID：$data[id]");

            return json(['code' => 1, 'msg' => '修改成功']);
        }
        $query = $account->get($id);
        $this->assign('user', $query);
        return $this->fetch();
    }

    /** 管理员操作 */
    public function adminAction(){
        return $this->fetch();
    }

    public function adminListAction(){
        $where = [];

        $where[] = ['status', 'in', '0,1'];
        !empty(input('id')) && $where[] = ['id', '=', input('id')];
        !empty(input('username')) && $where[] = ['username', 'like', "%".input('username')."%"];
        !empty(input('email')) && $where[] = ['email', 'like', "%".input('email')."%"];

        $account = model('AdminAccount');

        $query = $account->p()->where($where)-> with('AdminRole')->select();
        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $account->getAdminAccountCount($where),
            'data' => $query
        ]);
    }

    public function adminEditAction($id = 0){
        $account = model('AdminAccount');

        if($this->request->isPost()){
            $data = $this->request->post('a');

            $account->update($data,['id','=',$data['id']])
            && $this->log("管理员信息修改，ID：$id");

            return json(['code' => 1, 'msg' => '修改成功']);
        }

        $query = $account->get($id);

        $this->assign('admin', $query);
        return $this->fetch();
    }

    public function adminAddAction(){
        if($this->request->isPost()){
            $account = model('AdminAccount');
            $data = $this->request->post('a');

            ($id = $account->insertGetId($data))
            && $this->log("管理员添加，ID：$id");

            return json(['code' => 1, 'msg' => '添加成功']);
        }

        return $this->fetch();
    }

    public function adminDelAction($id = 0){
        $account = model('AdminAccount');
        $account->update(['status' => UserStatus::USER_DEL], ['id'=> $id])&& $this->log("管理员删除，ID：$id");

        return json(['code' => 1, 'msg' => '删除成功']);
    }

}