<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/2/2
 * Time: 16:23
 */

namespace app\admin\controller;


use app\common\library\Enumcode\LayuiJsonCode;

class Page extends Base
{
    /** 独立页面管理 */
    public function indexAction(){
        return $this->fetch();
    }

    public function pageListAction(){
        $page = model('Page');
        $query = $page ->field('id,route,title	,status')->order('id desc')->select();
        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $page->getPageCount(),
            'data' => $query
        ]);
    }
    public function addAction(){
        if($this->request->isPost()){
            $data = $this->request-> post('g');

            $page  = model('Page');

            ($id = $page->insertGetId($data, false))
            && $this->log("添加独立页面，ID：$id");

            return json(['code' => 1, 'msg' => '添加成功']);
        }

        return $this->fetch();
    }

    public function editAction($id = 0){
        $page = model('Page');
        if($this->request->isPost()){
            $data = $this->request->post('g');

            $page->update($data, ['id','=',$data['id']])
            && $this->log("修改独立页面，ID：$id");

            return json(['code' => 1, 'msg' => '修改成功']);
        }
        $query = $page->get($id);
        $this->assign('page', $query);
        return $this->fetch();
    }

    public function delAction($id = 0){
        $page = model('Page');
        $page->where('id', $id)->delete() && $this->log("删除独立页面，ID：$id");

        return json(['code' => 1, 'msg' => '删除成功']);
    }

}