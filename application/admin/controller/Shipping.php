<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/19
 * Time: 3:01
 */

namespace app\admin\controller;


use app\common\library\Enumcode\ShippingStatus;

class Shipping extends Base
{


    /** 物流模板 */
    public function indexAction(){
        return $this->fetch();
    }

    public function shippingListAction(){
        $modelShipping = model('Shipping');
        $where[] = ['status','<>', ShippingStatus::SHIPPING_DELETED];
        $query = $modelShipping ->where($where)->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $modelShipping->getShippingCount($where),
            'data' => $query
        ]);
    }

    public function addAction(){
        if($this->request->isPost()){
            $data = $this->request->post();

            $modelShipping = model('Shipping');

            try{
                $modelShipping->insert($data, false);
            }catch (\Exception $e){
                return json(['code' => 0, 'msg' => $e->getMessage()]);
            }

            return json(['code' => 1, 'msg' => '添加成功']);
        }

        return $this->fetch();
    }

    public function editAction(){
        $modelShipping = model('Shipping');
        if($this->request->isPost()){
            $data = $this->request->post();
            try{
                $modelShipping->update($data, ['id' => $data['id']]);
            }catch (\Exception $e){
                return json(['code' => 0, 'msg' => $e->getMessage()]);
            }

            return json(['code' => 1, 'msg' => '编辑成功']);
        }

        $id = input('get.id');
        $query = $modelShipping->where(['id' => $id]) -> find();
        $this->assign('shipping', $query);
        return $this->fetch();
    }

    public function delAction($id = 0){
        if(empty($id))
            return json(['code' => 0, 'msg' => '删除失败，物流模板不存在']);

        $modelShipping = model('Shipping');
        try{
            $modelShipping->update(['status' => ShippingStatus::SHIPPING_DELETED],['id' => $id]);
        } catch (\Exception $e){
            return json(['code' => 0, 'msg' => '删除失败']);
        }

        return json(['code' => 1, 'msg' => '删除成功']);
    }

}