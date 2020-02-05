<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/5
 * Time: 13:20
 */

namespace app\admin\controller;


class Article extends Base
{
    /** 文章操作 */
    public function indexAction(){
        return $this->fetch();
    }

    public function addAction(){
        if($this->request->isPost()){
            $modelArticle = new \app\common\model\Article();
            $data = $this->request->post();
            $data['update_time'] = time();
            $data['admin_id'] = $this->adminid();

            $modelArticle->insert($data);

            return json([
                'code' => 1,
                'msg' => '新建成功'
            ]);
        }

        return $this->fetch();
    }

    public function editAction(){
        $modelArticle = new \app\common\model\Article();

        if($this->request->isPost()){
            $data = $this->request->post();
            $data['update_time'] = time();

            $modelArticle->where([
                'id' => $data['id']
            ])->update($data);

            return json([
                'code' => 1,
                'msg' => '修改成功'
            ]);
        }

        $query = $modelArticle->where([
            'id' => input('get.id', '0', 'int')
        ])->find();

        $this->assign('article', $query);
        return $this->fetch();
    }

    public function delAction(){
        $modelArticle = new \app\common\model\Article();

        if($this->request->isPost()){

            $n = $modelArticle->where([
                'id' => explode(',',input('post.id'))
            ])->delete();

            return json([
                'code' => 1,
                'msg' => "共删除 $n 条记录"
            ]);
        }

        $modelArticle->where([
            'id' => input('get.id', '0', 'int')
        ])->delete();

        return json([
            'code' => '1',
            'msg' => '删除成功'
        ]);
    }

    public function articlelistAction(){
        $where = [];

        !empty(input('id')) && $where[] = ['id', '=', input('id')];
        !empty(input('title')) && $where[] = ['title', 'like', '%'.input('title').'%'];
        (input('status') != '') && $where[] = ['status', '=',input('status')];

        $modelArticle = new \app\common\model\Article();
        $query = $modelArticle->p()->with('AdminAccount') ->where($where)->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $modelArticle->getArticleCount($where),
            'data' => $query
        ]);
    }
}