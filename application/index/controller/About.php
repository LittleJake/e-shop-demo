<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/12/27
 * Time: 23:34
 */

namespace app\index\controller;


class About extends Common
{
    public function indexAction(){
        $this->assign('page_title', '关于我们');
        return $this->fetch();
    }

    public function certAction(){
        $this->assign('page_title', '资质');
        return $this->fetch();
    }

    public function trackAction($track_no = ''){
        if($this->request->isPost()){
            $track = model('Track');

            $query = $track -> where('track_no', $track_no)->cache(true, 600)->select();

            $this->assign('tracks',$query);
            return $this->fetch('about/ajaxTrack');
        }

        $this->assign('page_title', '药品溯源');
        return $this->fetch();
    }
}