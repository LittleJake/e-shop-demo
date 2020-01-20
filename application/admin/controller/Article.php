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
    public function indexAction(){
        return $this->fetch();
    }

    public function addAction(){
        return $this->fetch();
    }

    public function editAction(){
        return $this->fetch();
    }

    public function delAction(){
        return $this->fetch();
    }

    public function articlelistAction(){
        $modelArticle = new \app\common\model\Article();
        $query = $modelArticle->with('AdminAccount') ->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $modelArticle->getArticleCount(),
            'data' => $query
        ]);
    }
}