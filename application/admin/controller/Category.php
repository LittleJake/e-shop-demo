<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/9
 * Time: 13:43
 */

namespace app\admin\controller;


use app\common\library\Enumcode\LayuiJsonCode;

class Category extends Base
{
    /** 商品分类操作 */
    public function indexAction(){
        return $this->fetch();
    }

    public function addAction(){
        if ($this->request->isPost()) {
            $modelCategory = model('Category');

            $data = $this->request->post();
            try{
                $modelCategory->insert($data, false) && $this->log("添加分类 $data[name]");
            }catch (\Exception $e){
                return json(['code' => 0, 'msg' => '添加失败']);
            }

            return json(['code' => 1, 'msg' => '添加成功']);
        }


        return $this->fetch();
    }

    public function editAction($id = 0){
        $modelCategory = model('Category');
        if ($this->request->isPost()){

            $data = $this->request->post();
            try{
                $modelCategory -> update($data, ['id','=', $data['id']])
                && $this->log("修改分类 $data[name]，ID：$data[id]");
            } catch (\Exception $e){
                return json(['code' => 0, 'msg' => '修改失败']);
            }

            return json(['code' => 1, 'msg' => '修改成功']);
        }

        $this->assign('cate', $modelCategory->get($id));
        return $this->fetch();
    }

    public function delAction($id = 0){
        $category = model('Category');
        try{
            $category -> where('id' , $id)->delete()
            && $this->log("删除分类，ID：$id");
        } catch (\Exception $e){
            return json(['code' => 0, 'msg' => '删除失败']);
        }

        return json(['code' => 1, 'msg' => '删除成功']);
    }

    public function categoryListAction(){
        $modelCategory = model('Category');
        $query = $modelCategory ->withCount('Good')->select();
        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $modelCategory->getCategoryCount(),
            'data' => $query
        ]);
    }
}