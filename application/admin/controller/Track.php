<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/27
 * Time: 0:30
 */

namespace app\admin\controller;


class Track extends Base
{
    /** 溯源码追踪 */
    public function indexAction(){
        return $this->fetch();
    }

    public function addAction(){
        if($this->request->isPost()){
            $data = $this->request->post('g');
            $data['update_time'] = time();
            $track = model('Track');

            $track->insert($data, false);

            return json([
                'code' => 1,
                'msg' => '成功'
            ]);
        }

        return $this->fetch();
    }

    public function trackListAction(){
        $track = model('Track');
        $query = $track->p()->order('update_time desc')->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $track->getTrackCount(),
            'data' => $query
        ]);
    }
}