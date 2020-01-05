
layui.define(['table', 'form'], function(exports){
    var $ = layui.$
        ,table = layui.table
        ,form = layui.form,
        util = layui.util;

//文章管理
    table.render({
        elem: '#LAY-app-content-list'
        ,url: '/admin/article/articlelist' //模拟接口
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'id', width: 100, title: '文章ID', sort: true}
            ,{field: 'title', title: '文章标题'}
            ,{field: 'username', title: '作者', templet: function (d) {return d.admin_account.username;}}
            ,{field: 'update_time', title: '上传时间', sort: true, templet:function(d) {return util.toDateString(d.update_time*1000); }}
            ,{field: 'status', title: '发布状态', templet: '#buttonTpl', minWidth: 80, align: 'center'}
            ,{title: '操作', minWidth: 150, align: 'center', fixed: 'right', toolbar: '#table-content-list'}
        ]]
        ,page: true
        ,limit: 10
        ,limits: [10, 15, 20, 25, 30]
        ,text: '对不起，加载出现异常！'
    });

//监听工具条
    table.on('tool(LAY-app-content-list)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.confirm('确定删除此文章？', function(index){
                obj.del();
                layer.close(index);
            });
        } else if(obj.event === 'edit'){
            layer.open({
                type: 2
                ,title: '编辑文章'
                ,content: '../../../views/app/content/listform.html?id='+ data.id
                ,maxmin: true
                ,area: ['550px', '550px']
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    var iframeWindow = window['layui-layer-iframe'+ index]
                        ,submit = layero.find('iframe').contents().find("#layuiadmin-app-form-edit");

                    //监听提交
                    iframeWindow.layui.form.on('submit(layuiadmin-app-form-edit)', function(data){
                        var field = data.field; //获取提交的字段

                        //提交 Ajax 成功后，静态更新表格中的数据
                        //$.ajax({});
                        obj.update({
                            label: field.label
                            ,title: field.title
                            ,author: field.author
                            ,status: field.status
                        }); //数据更新

                        form.render();
                        layer.close(index); //关闭弹层
                    });

                    submit.trigger('click');
                }
            });
        }
    });

    exports('article', {})
});