<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <!-- 引入 ECharts 文件 -->
    <script src="echarts.js"></script>
    <script src="../jquery-3.3.1.min.js"></script>
</head>
<body>
    <!-- 为 ECharts 准备一个具备大小（宽高）的 DOM -->
    <div id="main" style="width: 600px;height:400px;"></div>
    <script type="text/javascript">
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('main'));

        // 指定图表的配置项和数据
        var option = {
            title: {
              text: 'ECharts 入门示例'
            },
            animation:false,
            tooltip: {},
            legend: {
              data:['销量','比例']
            },
            xAxis: {
              data: ["衬衫","羊毛衫","雪纺衫","裤子","高跟鞋","袜子"]
            },
            yAxis: [
              {type:'value',position:'left'},
              {type:'value',position:'right'}
            ],
            series: [{
              name: '销量',
              type: 'bar',
              data: [5, 20, 36, 10, 10, 20]
            },{
              name:'比例',
              type:'line',
              data:[22,33,44,55,66,77],
              yAxisIndex:1
            }]
        };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
        // $(document).ready(function(){
        //  var pic= myChart.getDataURL();
        // if(pic){
        // 　　$.ajax({
        //     type: "post",
        //       data: {
        // 　　　　　　baseimg: pic
        // 　　　　},
        // 　　　　url: 'savePic.php?action=save',
        // 　　　　async: true,
        // 　　　　success: function(data) {
        // 　　　　　　console.log(data);
        // 　　　　},
        // 　　　　error: function(err){
        // 　　　　　　console.log('图片保存失败');
        // 　　　　　　alert('图片保存失败');
        // 　　　　}
        // 　　});
        // }
        // })
    </script>
</body>
</html>