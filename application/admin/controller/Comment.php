<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/19
 * Time: 3:00
 */

namespace app\admin\controller;


use app\common\model\ArticleComment;

class Comment extends Base
{
    /** 文章评论（嵌套在文章列表） */
    public function indexAction(){
        return $this->fetch();
    }

    public function delAction($id = ''){
        $comment = model('ArticleComment');
        $comment -> where('id', $id)->delete();

        return json(['code' => 1, 'msg' => '删除成功']);
    }

    public function commentListAction(){
        $where = [];

        !empty(input('id')) && $where[] = ['id', '=', input('id')];
        !empty(input('content')) && $where[] = ['content', 'like', '%'.input('content').'%'];
        $where[] = ['article_id', '=', input('article_id')];

        $comm = model('ArticleComment');
        $query = $comm->p()->with([
            'Account' => function($q) {return $q->withField('id,username');},
        ])->where($where)->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $comm->getCommentCount($where),
            'data' => $query
        ]);
    }
}