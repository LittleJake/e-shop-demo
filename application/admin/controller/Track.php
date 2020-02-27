<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/27
 * Time: 0:30
 */

namespace app\admin\controller;


use app\common\library\Enumcode\LayuiJsonCode;

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

            ($id = $track->insertGetId($data, false))
            && $this->log("添加溯源信息，ID：$id");

            return json(['code' => 1, 'msg' => '成功']);
        }

        return $this->fetch();
    }

    public function trackListAction(){
        $track = model('Track');
        $query = $track->p()->order('update_time desc')->select();
        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $track->getTrackCount(),
            'data' => $query
        ]);
    }
}