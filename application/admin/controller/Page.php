<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/2/2
 * Time: 16:23
 */

namespace app\admin\controller;


class Page extends Base
{
    /** 独立页面管理 */
    public function indexAction(){
        return $this->fetch();
    }

    public function pageListAction(){
        $page = model('Page');
        $query = $page ->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $page->getPageCount(),
            'data' => $query
        ]);
    }
    public function addAction(){
        if($this->request->isPost()){
            $data = $this->request-> post('g');

            $page  = model('Page');

            $page->insert($data, false);

            return json([
                'code' => 1,
                'msg' => '成功'
            ]);
        }

        return $this->fetch();
    }

    public function editAction(){
        $page = model('Page');
        if($this->request->isPost()){
            $data = $this->request->post('g');

            $page->update($data, ['id'=>$data['id']]);

            return json([
                'code' => 1,
                'msg' => '成功'
            ]);
        }
        $data = input();
        $query = $page->get($data['id']);
        $this->assign('page', $query);
        return $this->fetch();
    }

    public function delAction(){
        $page = model('Page');
        $data = input();
        $page->where(['id'=> $data['id']])->delete();

        return json([
            'code' => 1,
            'msg' => '成功'
        ]);
    }

}