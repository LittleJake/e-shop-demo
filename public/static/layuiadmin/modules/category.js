layui.define(['table', 'form'], function(exports){
    var $ = layui.$
        ,table = layui.table
        ,form = layui.form;
//分类管理
    table.render({
        elem: '#LAY-app-category'
        ,url: '/admin/category/categoryList' //模拟接口
        ,cols: [[
            {field: 'id', width: 100, title: 'ID', sort: true, fixed: 'left'}
            ,{field: 'name', title: '分类名', minWidth: 100}
            ,{field: 'good_count', title: '商品数'}
            ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#layuiadmin-app-cont-tagsbar'}
        ]]
        ,text: {none: '暂无数据', error: '对不起，加载出现异常！'}
    });

//监听工具条
    table.on('tool(LAY-app-category)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.confirm('确定删除此分类？', function(index){
                obj.del();
                layer.close(index);
            });
        } else if(obj.event === 'edit'){
            var tr = $(obj.tr);
            layer.open({
                type: 2
                ,title: '编辑分类'
                ,content: '/admin/category/edit?id='+ data.id
                ,area: ['400px', '200px']
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    //点击确认触发 iframe 内容中的按钮提交
                    var iframeWindow = window['layui-layer-iframe'+ index],submit = layero.find('iframe').contents().find("#layuiadmin-app-cate-submit");
                    //监听提交
                    iframeWindow.layui.form.on('submit(layuiadmin-app-cate-submit)', function(data){
                        var field = data.field; //获取提交的字段

                        $.ajax({
                            url: '/admin/category/edit'
                            ,data: field
                            ,method: 'POST'
                            ,success:function(res){
                                if(res.code == 1){
                                    obj.update({
                                        name: field.name
                                    }); //数据更新

                                    form.render();
                                    layer.close(index);
                                }
                                layer.msg(res.msg,{
                                    icon: res.code
                                });
                            }
                        });
                    });
                    submit.trigger('click');

                }
            });
        }
    });


    exports('category', {})
});