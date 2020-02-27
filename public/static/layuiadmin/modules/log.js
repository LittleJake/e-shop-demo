layui.define(['table', 'form'], function(exports){
    var $ = layui.$
        ,table = layui.table
        ,util = layui.util;
    //分类管理
    table.render({
        elem: '#LAY-app-log'
        ,url: '/admin/log/logList' //模拟接口
        ,cols: [[
            {field: 'id', width: 70, title: 'ID', sort: true}
            ,{field: 'content', title: '操作'}
            ,{field: 'admin_id', width: 100,title: '管理员ID', templet: function(d) {return d.admin_account.id}}
            ,{field: 'username', title: '管理员名', templet: function(d) {return d.admin_account.username}}
            ,{field: 'update_time', title: '操作时间', templet:function(d){return util.toDateString(d.update_time*1000); }}
        ]]
        ,page: true
        ,limit: 10
        ,limits: [10, 15, 20, 25, 30]
        ,text: {none: '暂无数据', error: '对不起，加载出现异常！'}
    });

    exports('log', {})
});