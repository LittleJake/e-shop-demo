/**

 @Name：layuiAdmin 内容系统
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：LPPL

 */


layui.define(['table', 'form'], function(exports){
    var $ = layui.$
        ,table = layui.table
        ,util = layui.util
        ,form = layui.form;

    table.render({
        elem: '#LAY-app-good-list'
        ,url: '/admin/good/goodList'
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'id', width: 100, title: '商品ID', sort: true}
            ,{field: 'title', title: '商品标题', minWidth: 100}
            ,{field: 'subtitle', title: '商品副标题'}
            ,{field: 'name', title: '分类', templet: function(d){return d.category.name}}
            ,{field: 'status', title: '状态', templet: '#buttonTpl', minWidth: 80, align: 'center'}
            ,{title: '操作', width: 210, align: 'center', fixed: 'right', toolbar: '#table-good-list'}
        ]]
        ,page: true
        ,limit: 10
        ,limits: [10, 15, 20, 25, 30]
        ,text: {none: '暂无数据', error: '对不起，加载出现异常！'}
    });

    //监听工具条
    table.on('tool(LAY-app-good-list)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.confirm('确定删除此商品？', function(index){
                $.ajax({
                    type:'get',
                    url:'/admin/good/del?id='+data.id,
                    success:function (res) {
                        if (res.code == 1) {
                            //更新数据表
                            obj.del();
                            layer.close(index);
                            //关闭弹层
                        }
                        layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                    }
                });
            });
        } else if(obj.event === 'edit'){
            layer.open({
                type: 2
                ,title: '编辑商品'
                ,content: '/admin/good/edit.html?id='+ data.id
                ,maxmin: true
                ,area: ['800px', '500px']
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    var iframeWindow = window['layui-layer-iframe'+ index]
                        ,submit = layero.find('iframe').contents().find("#layuiadmin-app-good-submit");

                    //监听提交
                    iframeWindow.layui.form.on('submit(layuiadmin-app-good-submit)', function(data){
                        var field = data.field; //获取提交的字段

                        //提交 Ajax 成功后，静态更新表格中的数据
                        $.ajax({
                            type:'post',
                            url:'/admin/good/edit.html',
                            data: field,
                            success:function (res) {
                                if (res.code == 1) {
                                    //更新数据表
                                    //$.ajax({});

                                    table.reload('LAY-app-good-list'); //数据刷新

                                    form.render();
                                    layer.close(index); //关闭弹层
                                }
                                layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                            }
                        });
                    });
                    submit.trigger('click');
                }
            });
        }else if(obj.event === 'rate'){
            layer.open({
                type: 2
                ,title: '商品评价'
                ,content: '/admin/rate/index.html?good_id='+ data.id
                ,maxmin: true
                ,area: ['800px', '500px']
                ,btn: ['确定', '取消']
            });
        }
    });



    exports('page', {})
});