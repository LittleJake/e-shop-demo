<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/19
 * Time: 3:00
 */

namespace app\admin\controller;


class Comment extends Base
{
    /** 文章评论（嵌套在文章列表） */
    public function indexAction(){
        return $this->fetch();
    }

    public function delAction(){
        return $this->fetch();
    }

    public function commentListAction(){
        $comm = model('ArticleComment');
        $query = $comm->p()->with([
            'Account' => function($q){return $q->withField('id,user_name');},
        ])->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $comm->getCommentCount(),
            'data' => $query
        ]);
    }
}