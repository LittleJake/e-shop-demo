layui.define(['table', 'form'], function(exports){
    var $ = layui.$
        ,table = layui.table
        ,form = layui.form;
//分类管理
    table.render({
        elem: '#LAY-app-content-tags'
        ,url: layui.setter.base + 'json/content/tags.js' //模拟接口
        ,cols: [[
            {type: 'numbers', fixed: 'left'}
            ,{field: 'id', width: 100, title: 'ID', sort: true}
            ,{field: 'tags', title: '分类名', minWidth: 100}
            ,{field: 'num', title: '商品数'}
            ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#layuiadmin-app-cont-tagsbar'}
        ]]
        ,text: '对不起，加载出现异常！'
    });

//监听工具条
    table.on('tool(LAY-app-content-tags)', function(obj){
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
                ,content: '/admin/category/categoryEdit?id='+ data.id
                ,area: ['450px', '200px']
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    //获取iframe元素的值
                    var othis = layero.find('iframe').contents().find("#layuiadmin-app-form-tags")
                        ,tags = othis.find('input[name="tags"]').val();

                    if(!tags.replace(/\s/g, '')) return;

                    obj.update({
                        tags: tags
                    });
                    layer.close(index);
                }
                ,success: function(layero, index){
                    //给iframe元素赋值
                    var othis = layero.find('iframe').contents().find("#layuiadmin-app-form-tags").click();
                    othis.find('input[name="tags"]').val(data.tags);
                }
            });
        }
    });


    exports('category', {})
});