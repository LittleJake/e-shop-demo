/**

 @Name：layuiAdmin 用户管理 管理员管理 角色管理
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：LPPL

 */


layui.define(['table', 'form'], function(exports){
    var $ = layui.$
        ,table = layui.table
        ,form = layui.form;

    //用户管理
    table.render({
        elem: '#LAY-user-manage'
        ,url: '/admin/user/userList' //模拟接口
        ,cols: [[
            {field: 'id', width: 100, title: 'ID', sort: true}
            ,{field: 'username', title: '用户名', minWidth: 100}
            ,{field: 'mobile', title: '手机'}
            ,{field: 'email', title: '邮箱'}
            ,{field: 'status', title: '状态', templet: '#buttonTpl'}
            ,{title: '操作', width: 150, align:'center', fixed: 'right', toolbar: '#table-useradmin-webuser'}
        ]]
        ,page: true
        ,limit: 30
        ,height: 'full-220'
        ,text: '对不起，加载出现异常！'
    });

    //监听工具条
    table.on('tool(LAY-user-manage)', function(obj){
        var data = obj.data;
        if(obj.event === 'edit'){
            layer.open({
                type: 2
                ,title: '编辑用户'
                ,content: '/admin/user/userEdit?id='+data.id
                ,maxmin: true
                ,area: ['500px', '450px']
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    var iframeWindow = window['layui-layer-iframe'+ index]
                        ,submitID = 'LAY-user-front-submit'
                        ,submit = layero.find('iframe').contents().find('#'+ submitID);

                    //监听提交
                    iframeWindow.layui.form.on('submit('+ submitID +')', function(data){
                        var field = data.field; //获取提交的字段

                        //提交 Ajax 成功后，静态更新表格中的数据
                        $.ajax({
                            url: '/admin/user/userEdit',
                            data: field,
                            method: 'post',
                            success: function (e) {
                                if(e.code == 1){
                                    table.reload('LAY-user-manage');
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
                ,success: function(layero, index){

                }
            });
        }
    });

    //管理员管理
    table.render({
        elem: '#LAY-user-back-manage'
        ,url: '/admin/user/adminList' //模拟接口
        ,cols: [[
            {field: 'id', width: 80, title: 'ID', sort: true}
            ,{field: 'username', title: '登录名'}
            ,{field: 'email', title: '邮箱'}
            ,{field: 'name', title: '账户类型', templet: function(d){return d.admin_role.name}}
            ,{field: 'status', title:'状态', templet: '#buttonTpl', minWidth: 80, align: 'center'}
            ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#table-useradmin-admin'}
        ]]
        ,text: '对不起，加载出现异常！'
    });

    //监听工具条
    table.on('tool(LAY-user-back-manage)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.prompt({
                formType: 1
                ,title: '敏感操作，请验证口令'
            }, function(value, index){
                layer.close(index);
                layer.confirm('确定删除此管理员？', function(index){
                    $.ajax({
                        url: '/admin/user/adminDel?id='+data.id,
                        data: field,
                        method: 'get',
                        success: function (e) {
                            if(e.code == 1)
                                obj.del();
                            layer.msg(e.msg,{icon: e.code});
                            layer.close(index); //关闭弹层
                        }
                    });
                });
            });
        }else if(obj.event === 'edit'){
            layer.open({
                type: 2
                ,title: '编辑管理员'
                ,content: '/admin/user/adminEdit?id='+data.id
                ,area: ['420px', '420px']
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    var iframeWindow = window['layui-layer-iframe'+ index]
                        ,submitID = 'LAY-user-back-submit'
                        ,submit = layero.find('iframe').contents().find('#'+ submitID);

                    //监听提交
                    iframeWindow.layui.form.on('submit('+ submitID +')', function(data){
                        var field = data.field; //获取提交的字段

                        //提交 Ajax 成功后，静态更新表格中的数据
                        $.ajax({
                            url: '/admin/user/adminEdit',
                            data: field,
                            method: 'post',
                            success: function (e) {
                                if(e.code == 1)
                                    table.reload('LAY-user-back-manage');
                                layer.msg(e.msg,{icon: e.code});
                                layer.close(index); //关闭弹层
                            }
                        });
                    });

                    submit.trigger('click');
                }
                ,success: function(layero, index){

                }
            })
        }
    });

    //角色管理
    table.render({
        elem: '#LAY-user-back-role'
        ,url: layui.setter.base + 'json/useradmin/role.js' //模拟接口
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'id', width: 80, title: 'ID', sort: true}
            ,{field: 'rolename', title: '角色名'}
            ,{field: 'limits', title: '拥有权限'}
            ,{field: 'descr', title: '具体描述'}
            ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#table-useradmin-admin'}
        ]]
        ,text: '对不起，加载出现异常！'
    });

    //监听工具条
    table.on('tool(LAY-user-back-role)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.confirm('确定删除此角色？', function(index){
                obj.del();
                layer.close(index);
            });
        }else if(obj.event === 'edit'){
            var tr = $(obj.tr);

            layer.open({
                type: 2
                ,title: '编辑角色'
                ,content: '../../../views/user/administrators/roleform.html'
                ,area: ['500px', '480px']
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    var iframeWindow = window['layui-layer-iframe'+ index]
                        ,submit = layero.find('iframe').contents().find("#LAY-user-role-submit");

                    //监听提交
                    iframeWindow.layui.form.on('submit(LAY-user-role-submit)', function(data){
                        var field = data.field; //获取提交的字段

                        //提交 Ajax 成功后，静态更新表格中的数据
                        //$.ajax({});
                        table.reload('LAY-user-back-role'); //数据刷新
                        layer.close(index); //关闭弹层
                    });

                    submit.trigger('click');
                }
                ,success: function(layero, index){

                }
            })
        }
    });

    exports('useradmin', {})
});