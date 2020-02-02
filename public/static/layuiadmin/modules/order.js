/**

 @Name：layuiAdmin 内容系统
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：LPPL

 */


layui.define(['table', 'form', 'util'], function(exports){
    var $ = layui.$
        ,table = layui.table
        ,util = layui.util
        ,form = layui.form;

    table.render({
        elem: '#LAY-app-order-list'
        ,url: '/admin/order/orderlist'
        ,cols: [[
            {field: 'id', width: 60, title: 'ID', sort: true}
            ,{field: 'order_no', title: '订单号', width: 200}
            ,{field: 'total_price', title: '订单总价', templet: function(d){return "￥"+d.total_price}, width: 120}
            ,{field: 'status', title: '状态', templet: '#buttonTpl', width: 90, align: 'center'}
            ,{field: 'update_time', title: '更新时间', templet: function(d){return util.toDateString(d.update_time*1000); }, width: 200, align: 'center'}
            ,{title: '操作', minWidth: 210, align: 'center', fixed: 'right', toolbar: '#table-order-list'}
        ]]
        ,page: true
        ,limit: 10
        ,limits: [10, 15, 20, 25, 30]
        ,text: {none: '暂无数据', error: '对不起，加载出现异常！'}
    });

    //监听工具条
    table.on('tool(LAY-app-order-list)', function(obj){
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
        }  else if(obj.event === 'info'){
            layer.open({
                type: 2
                ,title: '商品明细'
                ,content: '/admin/order/info.html?id='+ data.id
                ,maxmin: true
                ,area: ['800px', '500px']
                ,btn: ['确定', '取消']
            });
        } else if(obj.event === 'ship'){
            layer.prompt({title: '输入运单号', formType: 3}, function(text, index){
                $.ajax({
                    url: '/admin/order/ship',
                    method: 'post',
                    data:{'id': data.id,'track_no': text},
                    success:function(res){
                        if (res.code == 1) {
                            table.reload('LAY-app-order-list'); //数据刷新
                            layer.close(index); //关闭弹层
                        }
                        layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                    }
                });
            });

        } else if(obj.event === 'shipinfo'){
            layer.open({
                type: 2,
                title: '物流详情',
                shadeClose: true,
                shade: false,
                maxmin: true, //开启最大化最小化按钮
                area: ['500px', '600px'],
                content: 'https://m.kuaidi100.com/app/query/?coname=base&nu='+data.track_no
            });
        }
    });

    //评论管理
    table.render({
        elem: '#LAY-app-content-comm'
        ,url: layui.setter.base + 'json/content/comment.js' //模拟接口
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'id', width: 100, title: 'ID', sort: true}
            ,{field: 'reviewers', title: '评论者', minWidth: 100}
            ,{field: 'content', title: '评论内容', minWidth: 100}
            ,{field: 'commtime', title: '评论时间', minWidth: 100, sort: true}
            ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#table-content-com'}
        ]]
        ,page: true
        ,limit: 10
        ,limits: [10, 15, 20, 25, 30]
        ,text: {none: '暂无数据', error: '对不起，加载出现异常！'}
    });

//监听工具条
    table.on('tool(LAY-app-content-comm)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.confirm('确定删除此条评论？', function(index){
                obj.del();
                layer.close(index);
            });
        } else if(obj.event === 'edit') {
            layer.open({
                type: 2
                ,title: '编辑评论'
                ,content: '../../../views/app/content/contform.html'
                ,area: ['450px', '300px']
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    var iframeWindow = window['layui-layer-iframe'+ index]
                        ,submitID = 'layuiadmin-app-comm-submit'
                        ,submit = layero.find('iframe').contents().find('#'+ submitID);

                    //监听提交
                    iframeWindow.layui.form.on('submit('+ submitID +')', function(data){
                        var field = data.field; //获取提交的字段

                        //提交 Ajax 成功后，静态更新表格中的数据
                        //$.ajax({});
                        table.reload('LAY-app-content-comm'); //数据刷新
                        layer.close(index); //关闭弹层
                    });

                    submit.trigger('click');
                }
                ,success: function(layero, index){

                }
            });
        }
    });

    exports('order', {})
});