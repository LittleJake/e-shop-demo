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
        ,url: '/admin/order/orderList'
        ,cols: [[
            {field: 'id', width: 60, title: 'ID', sort: true}
            ,{field: 'order_no', title: '订单号', width: 200}
            ,{field: 'total_price', title: '订单总价', templet: function(d){return "￥"+d.total_price}, width: 120}
            ,{field: 'status', title: '状态', templet: '#buttonTpl', width: 90, align: 'center'}
            ,{field: 'update_time', title: '更新时间', templet: function(d){return util.toDateString(d.update_time*1000); }, width: 200, align: 'center'}
            ,{title: '操作', width: 270, align: 'center', fixed: 'right', toolbar: '#table-order-list'}
        ]]
        ,page: true
        ,limit: 10
        ,limits: [10, 15, 20, 25, 30]
        ,text: {none: '暂无数据', error: '对不起，加载出现异常！'}
    });

    //监听工具条
    table.on('tool(LAY-app-order-list)', function(obj){
        var data = obj.data;
        if(obj.event === 'info'){
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

    table.render({
        elem: '#LAY-app-shipping-list'
        ,url: '/admin/order/shipList'
        ,cols: [[
            {field: 'id', width: 60, title: 'ID', sort: true}
            ,{field: 'order_no', title: '订单号', width: 200}
            ,{field: 'total_price', title: '订单总价', templet: function(d){return "￥"+d.total_price}, width: 120}
            ,{field: 'status', title: '状态', templet: '#buttonTpl', width: 90, align: 'center'}
            ,{field: 'update_time', title: '更新时间', templet: function(d){return util.toDateString(d.update_time*1000); }, width: 200, align: 'center'}
            ,{title: '操作', width: 270, align: 'center', fixed: 'right', toolbar: '#table-shipping-list'}
        ]]
        ,page: true
        ,limit: 10
        ,limits: [10, 15, 20, 25, 30]
        ,text: {none: '暂无数据', error: '对不起，加载出现异常！'}
    });

    //监听工具条
    table.on('tool(LAY-app-shipping-list)', function(obj){
        var data = obj.data;
        if(obj.event === 'info'){
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
                            table.reload('LAY-app-shipping-list'); //数据刷新
                            layer.close(index); //关闭弹层
                        }
                        layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                    }
                });
            });

        }
    });
    exports('order', {})
});