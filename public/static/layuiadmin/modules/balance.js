/**
 * 后台余额模块
 *
 *
 */


layui.define(['table', 'form'], function(exports){
    var $ = layui.$
        ,table = layui.table
        ,form = layui.form;

    //用户管理
    table.render({
        elem: '#LAY-balance-list'
        ,url: '/admin/balance/balanceList' //模拟接口
        ,cols: [[
            {field: 'id', width: 100, title: 'ID', sort: true}
            ,{field: 'username', title: '用户名', minWidth: 100, templet: function(e){return e.account.username}}
            ,{field: 'money', title: '金额'}
            ,{title: '操作', width: 80, align:'center', fixed: 'right', toolbar: '#table-balance-tool'}
        ]]
        ,page: true
        ,limit: 30
        ,height: 'full-220'
        ,text: {none: '暂无数据', error: '对不起，加载出现异常！'}
    });

    //监听工具条
    table.on('tool(LAY-balance-list)', function(obj){
        var data = obj.data;

        if(obj.event === 'change'){
            var tr = $(obj.tr);

            layer.open({
                type: 2
                ,title: '流水明细'
                ,content: '/admin/balance/change?uid='+data.user_id
                ,maxmin: true
                ,area: ['700px', '500px']
                ,btn: ['确定']
                ,yes: function(index, layero){
                    layer.close(index); //关闭弹层
                }
            });
        }
    });
    exports('balance', {})
});