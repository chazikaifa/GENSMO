<html>
<head>
	<title>本地故障单管理系统</title>
	<meta charset="UTF-8">
	<script type="text/javascript" src="scripts/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="scripts/jeDate/jedate/jquery.jedate.js"></script>
	<link rel="stylesheet" href="scripts/jeDate/jedate/skin/jedate.css">
	<script type="text/javascript">
		$(document).ready(function(){
			var is_search = true;
			var cur_page = 0;
			var result = {};
			var templet = $("#templet").clone();
			var parent = $("#order_list");
			var order_number = $("#order_number");
			var page_number = $("#page_number");
			
			function refresh_list(){
				let order_list = result.result;
				let	order_sum = result.sum;
				parent.empty();
				
				order_number.html(order_sum);
				page_number.html(Math.ceil(order_sum / $("#limit").val()));
				
				for(x in order_list){
					let list_item = templet.clone();
					let order_index = list_item.find("#order_index");
					let id = list_item.find("#id");
					let name = list_item.find("#name");
					let circuit_number = list_item.find("#circuit_number");
					let trouble_symptom = list_item.find("#trouble_symptom");
					let start_time = list_item.find("#start_time");
					let step = list_item.find("#step");
					let btn_view = list_item.find("#view");
					let btn_edit = list_item.find("#edit");
					let btn_delete = list_item.find("#delete");
					
					list_item.attr("id","order_" + (parseInt(x)+1));
					order_index.html(parseInt(x)+1);
					id.html(order_list[x].id);
					name.html(order_list[x].name);
					circuit_number.html(order_list[x].circuit_number);
					trouble_symptom.html(order_list[x].trouble_symptom);
					start_time.html(order_list[x].start_time);
					step.html(order_list[x].step);
					
					btn_view.click(function(){
						console.log("view");
					});
					
					btn_edit.click(function(){
						console.log("edit");
					});
					
					btn_delete.click(function(){
						console.log("delete");
					});
					parent.append(list_item);
				}
			}
			function get_list(){
				$.ajax({
					type:"POST",
					data:{
						id:$("#id_input").val(),
						name:$("#name_input").val(),
						start_time_start:$("#start_time_start").val(),
						start_time_end:$("#start_time_end").val(),
						end_time_start:$("#end_time_start").val(),
						end_time_end:$("#end_time_end").val(),
						number:$("#number_input").val(),
						index:cur_page*$("#limit").val(),
						limit:$("#limit").val()
					},
					url:"getList.php",
					dataType: 'json',
					timeout: 5000,
					beforeSend:function(){
						
					},
					error:function(e){
						alert(e.responseText);
					},
					success:function(data){
						//var json = eval(data);
						console.log(data);
						result = data;
						// order_list = data.result;
						// order_sum = data.sum;
						refresh_list();
					}
				});
			}
			$("#btn_search").click(function(){
				if(is_search){
					$("#search_container").attr("class","hidden");
					get_list();
				}else{
					$("#search_container").attr("class","");
				}
				is_search = !is_search;
			});
			
			$("#start_time_start").jeDate({
				format:"YYYY-MM-DD hh:mm:ss",
				choosefun:function(elem,datas){
					console.log(datas);
				}
			});
			
			$("#start_time_end").jeDate({
				format:"YYYY-MM-DD hh:mm:ss",
				choosefun:function(elem,datas){
					console.log(datas);
				}
			});
			
			$("#end_time_start").jeDate({
				format:"YYYY-MM-DD hh:mm:ss",
				choosefun:function(elem,datas){
					console.log(datas);
				}
			});
			
			$("#end_time_end").jeDate({
				format:"YYYY-MM-DD hh:mm:ss",
				choosefun:function(elem,datas){
					console.log(datas);
				}
			});
			
			$("#btn_refresh").click(function(){
				get_list();
			});
			
			$("#btn_new").click(function(){
				console.log("new");
			});
		});
	</script>
	<style type="text/css">
		body{
			height: 100vh;
			width: 100vw;
			overflow : hidden;
			margin: 0;
			padding: 0;
		}
		#title{
			width:100vw;
			height: 8vh;
			line-height:8vh;
			font-size: 3vh;
			text-align: center;
			background: #EA7777;
			color: #FFFFFF;
		}
		
		#navigate_bar{
			width:15vw;
			height: 92vh;
		}
		
		.bar_head{
			background: #EEEEEE;
			height:5vh;
			font-size:2vh;
			text-align:center;
			line-height:5vh;
		}
		
		.bar_item{
			background: #FFFFFF;
			height:5vh;
			font-size:2vh;
			text-align:center;
			line-height:5vh;
		}
		
		.bar_item:hover{
			background: #AAAAAA;	
		}
		
		#container{
			position: absolute;
			left: 15vw;
			top:8vh;
			width:85vw;
			height:92vh;
			background:#FAFAFA;
		}
		
		#search_container{
			overflow: hidden;
			transition: all 0.5s;
		}
		
		#search_container.hidden{
			height: 0;
		}
		
		.search{
			display:flex;
			align-items: center;
			height: 5vh;
			width:80vw;
		}
		
		.search_title{
			display: inline-block;
			font-size:2vh;
			height:2vh;
			line-height:2vh;
			text-align:center;
			margin:2vw;
		}
		
		.input {
			display: inline-block;
			width: 12vw;
			height: 2.5vh;
			line-height:2.5vh;
			background: #FFFFFF;
			border: 1px solid #EAEAEA;
			padding-left:0.2vw;
			padding-right:0.2vw;
		}
		
		#btn_search{
			height: 4vh;
			line-height: 4vh;
			border-radius: 1vh;
			background: #EA7777;
			width: 9vh;
			text-align: center;
			color: #FFFFFF;
			font-size:2vh;
			margin: 2vh;
		}
		
		#btn_search:active{
			background: #D96666;
		}
		
		#order_list{
			height: 40vh;
			margin:0;
			padding:0;
			overflow-y:auto;
			overflow-x:hidden;
		}
		li{
			list-style: none;
		}
		#list_head{
			background:#EA7777;
			color:#FFFFFF;
			height:3vh;
			margin-left:5vw;
			border-bottom:1px solid #000000;
			border-top:1px solid #000000;
			border-left:1px solid #000000;
			width: fit-content;
		}
		.list_item{
			border-bottom:1px solid #000000;
			margin-left:5vw;
			height:6vh;
			width: fit-content;
			border-left:1px solid #000000;
		}
		.item{
			display: inline-block;
			width: 10vw;
			border-right: 1px solid #000000;
			margin-left: -0.5vw;
			height: 6vh;
			line-height: 6vh;
			text-align:center;
			font-size:1vw;
			overflow:hidden;
		}
		.item.index{
			width: 5vw;
		}
		.item.head{
			height: 3vh;
			line-height: 3vh;
		}
		.item.head > .content{
			height: 3vh;
		}
		.content{
			line-height: normal;
			height:6vh;
			display:flex;
			align-items:center;
			justify-content:center;
		}
		
		#templet{
			display:none;
		}
		
		#list_foot{
			display:flex;
			align-items:center;
			margin-top:1vh;
			font-size: 1.2vw;
			position:relative;
		}
		
		.btn_img{
			height: 2vh;
			width: 2vh;
			border-radius: 1vh;
			background: #EA7777;
			padding:0.5vh;
			margin: 0.2vh;
		}
		
		.btn_img:active{
			background: #D96666;
		}
		
		#btn_refresh{
			margin-left: 2vh;
		}
		
		#limit{
			width: 2.5vw;
		}
		
		#page_control{
			display: flex;
			align-items:center;
			position:absolute;
			right:2vw;
			color: #EA7777;
		}
		
		#to_first{
			margin-left:2vw;
		}
		
		.btn_page{
			display:inline-block;
			text-align:center;
			line-height:2vh;
			font-size:1.5vh;
			height: 2vh;
			width: 2vh;
			margin:0;
			border: 1px solid #DEDEDE;
		}
		
		.btn_page > img {
			height: 2vh;
			width: 2vh;
		}
		
		.btn_page:hover{
			background: #DEDEDE;
		}
		
		.btn_page:active{
			background: #CDCDCD;
		}
		
		.btn_page.select{
			background: #EA7777;
			color:#FFFFFF
		}
		
	</style>
</head>
<body>
	<div id="title">政企网络服务中台本地故障单管理系统</div>
	<div id="navigate_bar">
			<div class="bar_head"><b>导航栏</b></div>
			<div class="bar_item">所有工单</div>
			<div class="bar_item">已结单</div>
			<div class="bar_item">未结单</div>
			<div class="bar_item">已撤单</div>
			<div class="bar_item">工单导出</div>
	</div>
	<div id="container">
		<div id="search_container">
			<div id="id_search" class="search">
				<span class="search_title">故障编号:</span>
				<input id="id_input" type="text" class="input" value=""/>
			</div>
			<div id="name_search" class="search">
				<span class="search_title">客户名称:</span>
				<input id="name_input" type="text" class="input" value=""/>
			</div>
			<div id="start_time_search" class="search">
				<span class="search_title">故障发生时间:</span>
				<input id="start_time_start" type="text" class="input" placeholder="请选择" readonly/>
				<span class="search_title">到</span>
				<input id="start_time_end" type="text" class="input" placeholder="请选择" readonly/>
			</div>
			<div id="end_time_search" class="search">
				<span class="search_title">故障恢复时间:</span>
				<input id="end_time_start" type="text" class="input" placeholder="请选择" readonly/>
				<span class="search_title">到</span>
				<input id="end_time_end" type="text" class="input" placeholder="请选择" readonly/>
			</div>
			<div id="number_search" class="search">
				<span class="search_title">电路编号:</span>
				<input id="number_input" type="text" class="input" value=""/>
			</div>
		</div>
		<div id="btn_search">查询</div>
		<div id="list_head">
			<span class="index item head"><span class="content">序号</span></span>
			<span class="item head"><span class="content">故障单编号</span></span>
			<span class="item head"><span class="content">客户名称</span></span>
			<span class="item head"><span class="content">电路编号</span></span>
			<span class="item head"><span class="content">故障简述</span></span>
			<span class="item head"><span class="content">故障发生时间</span></span>
			<span class="item head"><span class="content">工单状态</span></span>
			<span class="item head"><span class="content">操作</span></span>
		</div>
		<ul id="order_list">
			<li id="templet" class="list_item">
				<div>
					<span class="index item"><span id="order_index" class="content">0</span></span>
					<span class="item"><span id="id" class="content">B20190425-0001</span></span>
					<span class="item"><span id="name" class="content">XXXXXXXX公司</span></span>
					<span class="item"><span id="circuit_number" class="content">中山大道NE00001</span></span>
					<span class="item"><span id="trouble_symptom" class="content">不通</span></span>
					<span class="item"><span id="start_time" class="content">2019-04-25 09：30：23</span></span>
					<span class="item"><span id="step" class="content">结单</span></span>
					<span class="item"><span class="content">
						<img id="view" class="btn_img" src="img/view.png">
						<img id="edit" class="btn_img" src="img/edit.png">
						<img id="delete" class="btn_img" src="img/delete.png">
					</span></span>
				</div>
			</li>
		</ul>
		<div id="list_foot">
			<img id="btn_refresh" class="btn_img" src="img/refresh.png"/>
			<img id="btn_new" class="btn_img" src="img/new.png"/>
			<div id="page_control">
				<span>查询出记录</span>
				<span id="order_number">0</span>
				<span>条，每页</span>
				<input id="limit" style="number" value="10"/>
				<span>条，共</span>
				<span id="page_number">0</span>
				<span>页</span>
				<span id="to_first" class="btn_page"><img src="img/to_first.png"/></span>
				<span id="to_pre" class="btn_page"><img src="img/to_pre.png"/></span>
				<span id="page1" class="btn_page select">1</span>
				<span id="page2" class="btn_page">2</span>
				<span id="page3" class="btn_page">3</span>
				<span id="page4" class="btn_page">4</span>
				<span id="page5" class="btn_page">5</span>
				<span id="to_next" class="btn_page"><img src="img/to_next.png"/></span>
				<span id="to_last" class="btn_page"><img src="img/to_last.png"/></span>
		</div>
	</div>
</body>
</html>