layui.define(['table', 'form'], function(exports){
    var $ = layui.$
        ,table = layui.table
        ,form = layui.form;
//分类管理
    table.render({
        elem: '#LAY-app-shipping'
        ,url: '/admin/shipping/shippingList' //模拟接口
        ,cols: [[
            {field: 'id', width: 100, title: 'ID', sort: true, fixed: 'left'}
            ,{field: 'name', title: '物流名', minWidth: 100}
            ,{field: 'price', title: '物流价格', templet:function (e) {return "￥"+e.price}},{field: 'status', title: '状态', templet: '#buttonTpl'}
            ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#layuiadmin-app-shipping-tool'}
        ]]
        ,page: true
        ,limit: 10
        ,limits: [10, 15, 20, 25, 30]
        ,text: {none: '暂无数据', error: '对不起，加载出现异常！'}
    });

//监听工具条
    table.on('tool(LAY-app-shipping)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.confirm('确定删除此运输方式？', function(index){

                //提交 Ajax 成功后，静态更新表格中的数据
                $.ajax({
                    type:'get',
                    url:'/admin/shipping/del?id='+data.id,
                    success:function (res) {
                        if (res.code == 1) {
                            obj.del();
                            layer.close(index);
                        }
                        layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                    }
                });

            });
        } else if(obj.event === 'edit'){
            var tr = $(obj.tr);
            layer.open({
                type: 2
                ,title: '编辑运输方式'
                ,content: '/admin/shipping/edit?id='+ data.id
                ,area: ['400px', '320px']
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    var iframeWindow = window['layui-layer-iframe'+ index]
                        ,submitID = 'layuiadmin-app-shipping-submit'
                        ,submit = layero.find('iframe').contents().find("#"+submitID);

                    //监听提交
                    iframeWindow.layui.form.on('submit('+submitID+')', function(data){
                        var field = data.field; //获取提交的字段

                        //提交 Ajax 成功后，静态更新表格中的数据
                        $.ajax({
                            type:'post',
                            url:'/admin/shipping/edit.html',
                            data: field,
                            success:function (res) {
                                if (res.code == 1) {

                                    table.reload('LAY-app-shipping'); //数据刷新

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
        }
    });


    exports('shipping', {})
});