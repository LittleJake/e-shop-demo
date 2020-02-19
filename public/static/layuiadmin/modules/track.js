layui.define(['table', 'form'], function(exports){
    var $ = layui.$
        ,table = layui.table
        ,util = layui.util
        ,form = layui.form;
//分类管理
    table.render({
        elem: '#LAY-app-track'
        ,url: '/admin/track/trackList' //模拟接口
        ,cols: [[
            {field: 'id', width: 100, title: 'ID', sort: true, fixed: 'left'}
            ,{field: 'track_no', title: '溯源码', minWidth: 100}
            ,{field: 'name', title: '操作名称'}
            ,{field: 'update_time', title: '操作时间', width: 210, sort: true, templet: function(e){return util.toDateString(e.update_time*1000);}}
        ]]
        ,page: true
        ,limit: 10
        ,limits: [10, 15, 20, 25, 30]
        ,text: {none: '暂无数据', error: '对不起，加载出现异常！'}
    });

    exports('track', {})
});