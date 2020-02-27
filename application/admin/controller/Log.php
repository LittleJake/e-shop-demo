<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/2/6
 * Time: 13:29
 */

namespace app\admin\controller;


use app\common\library\Enumcode\LayuiJsonCode;

class Log extends Base
{
    /** 操作记录 */
    public function indexAction(){
        return $this->fetch();
    }

    public function logListAction(){
        $log = model('AdminLog');
        $query = $log->with([
            'AdminAccount' => function($query){
                return $query->withField('id,username');
            }
        ]) ->select();
        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $log->getLogCount(),
            'data' => $query
        ]);
    }

}