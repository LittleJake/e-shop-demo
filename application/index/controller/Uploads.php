<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/3/22
 * Time: 13:28
 */

namespace app\index\controller;


class Uploads extends Common
{
    public function indexAction(){
        $image = imagecreatefrompng('./static/img/null.png');
        header("Content-Type: image/png");
        imagepng($image);
        exit();
    }
}