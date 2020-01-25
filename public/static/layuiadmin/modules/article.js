
layui.define(['table', 'form'], function(exports){
    var $ = layui.$
        ,table = layui.table
        ,form = layui.form,
        util = layui.util;

//文章管理
    table.render({
        elem: '#LAY-app-article-list'
        ,url: '/admin/article/articlelist' //模拟接口
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'id', width: 70, title: 'ID', sort: true}
            ,{field: 'title', title: '文章标题'}
            ,{field: 'username', width: 130, title: '作者', templet: function (d) {return d.admin_account.username;}}
            ,{field: 'update_time', title: '时间', width: 180, sort: true, templet:function(d) {return util.toDateString(d.update_time*1000); }}
            ,{field: 'status', title: '状态', templet: '#buttonTpl', width: 80, align: 'center'}
            ,{title: '操作', minWidth: 200, align: 'center', fixed: 'right', toolbar: '#table-article-list'}
        ]]
        ,page: true
        ,limit: 10
        ,limits: [10, 15, 20, 25, 30]
        ,text: '对不起，加载出现异常！'
    });

//监听工具条
    table.on('tool(LAY-app-article-list)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.confirm('确定删除此文章？', function(index){
                $.ajax({
                    url: '/admin/article/del?id='+data.id,
                    method: 'get',
                    success: function (e) {
                        if(e.code == 1){
                            obj.del();
                        }
                        layer.msg(e.msg,{
                            icon: e.code
                        });
                        layer.close(index); //关闭弹层
                    }
                });

                layer.close(index);
            });
        } else if(obj.event === 'edit'){
            layer.open({
                type: 2
                ,title: '编辑文章'
                ,content: '/admin/article/edit?id='+ data.id
                ,maxmin: true
                ,area: ['800px', '500px']
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    var iframeWindow = window['layui-layer-iframe'+ index]
                        ,submit = layero.find('iframe').contents().find("#layuiadmin-article-form-submit");

                    //监听提交
                    iframeWindow.layui.form.on('submit(layuiadmin-article-form-submit)', function(data){
                        var field = data.field; //获取提交的字段

                        //提交 Ajax 成功后，静态更新表格中的数据
                        $.ajax({
                            url: '/admin/article/edit',
                            data: field,
                            method: 'post',
                            success: function (e) {
                                if(e.code == 1){
                                    obj.update({
                                        title: field.title
                                        ,status: field.status
                                        ,update_time: util.toDateString(field.update_time*1000)
                                    }); //数据更新

                                    form.render();
                                }
                                layer.msg(e.msg,{
                                    icon: e.code
                                });
                                layer.close(index); //关闭弹层
                            }
                        });
                    });

                    submit.trigger('click');
                }
            });
        }
    });

    exports('article', {})
});