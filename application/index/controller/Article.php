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
                    return $query->field('id,username')->find();
                }
            ])
            -> where(['status' => 1])
            ->paginate(15);

        $this->assign('articles', $article);
        $this->assign('page_title', '文章列表');
        return $this->fetch();
    }

    public function infoAction($id = 0){
        $article = model('Article');

        $data = $article->with([
            'AdminAccount' => function($e){
                return $e->withField('id,username');
            }
        ])->get($id);


        $comment=model('ArticleComment');

        $query  = $comment->where('article_id' , $id)->with(['Account'=>function($q){return $q-> withField('id,user_name');}])->paginate(10);

        $page = $query -> render();
        $this->assign('page', $page);
        $this->assign('page_title', '文章 '. $data['title']);
        $this->assign('data', $data);
        $this->assign('comment', $query);
        return $this->fetch();
    }

    public function commentAction($id = 0){
        $article = model('Article');

        $data = $article->with(['AdminAccount' => function($e){return $e->withField('id,username');}])->get($id);


        $this->assign('page_title', '文章 '. $data['title']);
        $this->assign('data', $data);
        return $this->fetch();
    }
}