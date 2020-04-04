<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jake
 * Date: 12/23/2019
 * Time: 1:19 PM
 *
 * Stay simple, stay naive.
 *
 */

namespace app\admin\controller;

use app\common\library\Enumcode\GoodStatus;
use app\common\library\Enumcode\LayuiJsonCode;

class Good extends Base
{
    /** 商品操作 */
    public function indexAction(){
        $category = model('Category');

        $query = $category -> where([
            'parent_id' => 0
        ])->select();

        $this -> assign('category', $query);
        return $this->fetch();
    }

    public function addAction(){
        if($this->request->isPost()){
            $good = model('Good');
            $g = $this->request->post('g');
            $c = $this->request->post('c');
            $c['name'] = '默认';
            try{
                ($id = $good->insertGetId($g))
                && $good ->GoodCat()->insert(array_merge(['good_id'=> $id], $c))
                    && $this->log("添加商品，ID：$id");
            } catch (\Exception $e){
                return json(['code' => 0, 'msg' => '添加失败']);
            }

            return json(['code' => 1, 'msg' => '添加成功']);
        }

        $category = model('Category');

        $this->assign('cate', $category->select());
        return $this->fetch();
    }

    public function editAction($id = 0){
        $good = model('Good');
        if($this->request->isPost()){
            $g = $this->request->post('g');
            $c = $this->request->post('c');
            try{
                $good->update($g, ['id'=> $g['id']])
                &&$good->GoodCat()->update($c,['good_id' =>  $g['id']])
                &&$this->log("修改商品信息，ID：$g[id]");

            }catch (\Exception $e){
                return json(['code' => 0, 'msg' => '修改失败']);
            }

            return json(['code' => 1, 'msg' => '修改成功']);
        }

        $category = model('Category');

        $this->assign('good', $good -> with('GoodCat')->get($id));
        $this->assign('cate', $category -> select());

        return $this->fetch();
    }

    public function goodListAction(){
        $where = [];

        !empty(input('id')) && $where[] = ['id', '=', input('id')];
        !empty(input('cate_id')) && $where[] = ['cate_id', '=', input('cate_id')];
        !empty(input('title')) && $where[] = ['title', 'like', '%'.input('title').'%'];
        if(input('status') != '')
            $where[] = ['status', '=',input('status')];
        else
            $where[] = ['status', '<>', GoodStatus::GOOD_DELETE];

        $good = model('Good');
        $query = $good ->p()-> with('Category') -> where($where)->field('id,cate_id,title,status,subtitle')->select();
        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $good->getGoodCount($where),
            'data' => $query
        ]);
    }

    public function delAction($id = 0){
        $good = model('Good');
        try{
            $good->update(['status' => GoodStatus::GOOD_DELETE],['id' ,'=', $id])
            && $this->log("删除商品信息，ID：$id");
        } catch (\Exception $e){
            return json(['code' => 0, 'msg' => '删除失败']);
        }

        return json(['code' => 1, 'msg' => '删除成功']);
    }
}
