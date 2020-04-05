<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/5
 * Time: 13:20
 */

namespace app\admin\controller;


use app\common\library\Enumcode\LayuiJsonCode;

class Article extends Base
{
    /** 文章操作 */
    public function indexAction(){
        return $this->fetch();
    }

    public function addAction(){
        if($this->request->isPost()){
            $article = model('Article');
            $data = $this->request->post();
            $data['update_time'] = time();
            $data['admin_id'] = $this->adminid();

            try{
                ($id = $article->insertGetId($data))&&$this->log("新建文章，ID：$id");
            } catch (\Exception $e){
                return json(['code' => 0, 'msg' => '新建失败']);
            }

            return json([
                'code' => 1,
                'msg' => '新建成功'
            ]);
        }

        return $this->fetch();
    }

    public function editAction($id = 0){
        $article = model('Article');

        if($this->request->isPost()){
            $data = $this->request->post();
            $data['update_time'] = time();
            try{
                $article->update($data,['id','=',$data['id']]) && $this->log("修改文章，ID：$data[id]");
            } catch (\Exception $e){
                return json(['code' => 0, 'msg' => '修改失败']);
            }
            return json(['code' => 1, 'msg' => '修改成功']);
        }

        $this->assign('article', $article->get($id));
        return $this->fetch();
    }

    public function delAction($id = 0){
        $article = model('Article');

        if($this->request->isPost()){
            try{
                ($n = $article->where([
                    'id' => explode(',',input('post.id'))
                ])->delete())
                && $this->log('批量删除文章，ID:'.input('post.id'));
            } catch (\Exception $e){
                return json(['code' => 0, 'msg' => '删除失败']);
            }

            return json([
                'code' => 1,
                'msg' => "共删除 $n 条记录"
            ]);
        }

        try{
            $article->where(['id' => $id])->delete()
            && $this->log("删除文章，ID:$id");
        } catch (\Exception $e){
            return json(['code' => 0, 'msg' => '删除失败']);
        }

        return json([
            'code' => 1,
            'msg' => '删除成功'
        ]);
    }

    public function articleListAction(){
        $where = [];

        !empty(input('id')) && $where[] = ['id', '=', input('id')];
        !empty(input('title')) && $where[] = ['title', 'like', '%'.input('title').'%'];
        (input('status') != '') && $where[] = ['status', '=',input('status')];

        $article = model('Article');
        $query = $article->p()->with('AdminAccount') ->where($where)->field('title,status,id,update_time,admin_id')->order('id desc')->select();

        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $article->getArticleCount($where),
            'data' => $query
        ]);
    }
}