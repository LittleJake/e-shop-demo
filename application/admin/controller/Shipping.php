<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/19
 * Time: 3:01
 */

namespace app\admin\controller;


class Shipping extends Base
{


    /** 物流模板 */
    public function indexAction(){
        return $this->fetch();
    }

    public function shippinglistAction(){
        $modelShipping = new \app\common\model\Shipping();
        $query = $modelShipping ->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $modelShipping->getShippingCount(),
            'data' => $query
        ]);
    }

    public function addAction(){
        if($this->request->isPost()){
            $data = $this->request->post();

            $modelShipping = new \app\common\model\Shipping();

            try{
                $modelShipping->insert($data, false);
            }catch (\Exception $e){
                return json(['code' => 0, 'msg' => $e->getMessage()]);
            }

            return json(['code' => 1, 'msg' => 'success']);
        }

        return $this->fetch();
    }

    public function editAction(){
        $modelShipping = new \app\common\model\Shipping();
        if($this->request->isPost()){
            $data = $this->request->post();
            try{
                $modelShipping->update($data, ['id' => $data['id']]);
            }catch (\Exception $e){
                return json(['code' => 0, 'msg' => $e->getMessage()]);
            }

            return json(['code' => 1, 'msg' => 'success']);
        }

        $id = input('get.id');
        $query = $modelShipping->where(['id' => $id]) -> find();
        $this->assign('shipping', $query);
        return $this->fetch();
    }

    public function delAction($id = 0){
        if(empty($id))
            return json(['code' => 0, 'msg' => 'failed']);

        $modelShipping = new \app\common\model\Shipping();
        try{
            $modelShipping->where(['id' => $id])->delete();
        } catch (\Exception $e){
            return json(['code' => 0, 'msg' => 'failed']);
        }

        return json(['code' => 1, 'msg' => 'success']);
    }

}