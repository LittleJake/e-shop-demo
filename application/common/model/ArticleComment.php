<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/12/27
 * Time: 23:05
 */

namespace app\common\model;


class ArticleComment extends BaseModel
{
    public function Account(){
        return $this->belongsTo('Account','user_id', 'id');
    }

    public function Article(){
        return $this->belongsTo('Article','article_id', 'id');
    }

    public function getCommentCount($where = [],$field ='*'){
        return parent::getCount($where, $field);
    }

}