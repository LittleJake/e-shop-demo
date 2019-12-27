<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jake
 * Date: 12/26/2019
 * Time: 8:52 PM
 *
 * Stay simple, stay naive.
 *
 */

namespace app\index\controller;


class Article extends Common
{
    public function indexAction(){
        $modelArticle = new \app\common\model\Article();


        $article = $modelArticle
            -> with([
                'AdminAccount' => function($query){
                    $query->field('id,username')->find();
                }
            ])
            -> where(['status' => 1])
            ->paginate(10);

        $this->assign('articles', $article);
        $this->assign('page_title', '文章列表');
        return $this->fetch();
    }

    public function infoAction(){
        $this->assign('page_title', '文章');
        return $this->fetch();
    }
}