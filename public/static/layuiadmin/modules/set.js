/**

 @Name：layuiAdmin（iframe版） 设置
 @Author：贤心
 @Site：http://www.layui.com/admin/
 @License: LPPL

 */

layui.define(['form', 'upload'], function(exports){
    var $ = layui.$
        ,layer = layui.layer
        ,laytpl = layui.laytpl
        ,setter = layui.setter
        ,view = layui.view
        ,admin = layui.admin
        ,form = layui.form
        ,upload = layui.upload;

    var $body = $('body');

    //网站设置
    form.on('submit(set_website)', function(obj){
        $.ajax({
            method: 'post',
            url: '/admin/set/index',
            data: obj.field,
            success: function (res) {
                layer.msg(res.msg);
            }
        });

        return false;
    });

    //邮件服务
    form.on('submit(set_system_email)', function(obj){
        layer.msg(JSON.stringify(obj.field));

        return false;
    });


    //设置我的资料
    form.on('submit(setmyinfo)', function(obj){
        $.ajax({
            method: 'post',
            url: '/admin/set/user',
            data: obj.field,
            success: function (res) {
                layer.msg(res.msg);
            }
        });
        return false;
    });

    //设置密码
    form.on('submit(setmypass)', function(obj){
        $.ajax({
            method: 'post',
            url: '/admin/set/passwd',
            data: obj.field,
            success: function (res) {
                layer.msg(res.msg);
            }
        });
        return false;
    });

    //对外暴露的接口
    exports('set', {});
});