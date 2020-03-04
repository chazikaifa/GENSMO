<html>
<head>
	<title>本地故障单管理系统</title>
	<meta charset="UTF-8">
	<script type="text/javascript" src="scripts/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="scripts/jeDate/jedate/jquery.jedate.js"></script>
	<script type="text/javascript" src="scripts/selectordie.min.js"></script>
	<link rel="stylesheet" href="scripts/jeDate/jedate/skin/jedate.css">
	<link rel="stylesheet" href="css/selectordie.css">
	<link rel="stylesheet" href="css/selectordie_theme_01.css">
	<script type="text/javascript">
		$(document).ready(function(){
			var is_search = false;
			var result = {};
			var templet = $("#templet").clone();
			var parent = $("#order_list");
			var order_number = $("#order_number");
			var page_number = $("#page_number");
			var page_group = new Array();
			var index = 0;
			for(let i=0;i<5;i++){
				page_group[i] = $("#page"+(i+1));
			}
			
			var sql_step = "未结单|挂起中";
			
			function refresh_list(cur_page){
				let order_list = result.result;
				let	order_sum = result.sum;
				parent.empty();
				
				let page = Math.ceil(order_sum / $("#limit").val());
				if(page > 5){
					for(let i=0;i<5;i++){
						page_group[i].attr("class","btn_page");
					}
					let start_num;
					if(cur_page >= page || cur_page < 0){
						page_group[0].attr("class","btn_page select");
						index = 0;
						start_num = 1;
					}else if(cur_page > 1 && cur_page + 2 < page){
						start_num = cur_page - 1;
						page_group[2].attr("class","btn_page select");
					}else if(cur_page <= 1){
						start_num = 1;
						page_group[cur_page].attr("class","btn_page select");
					}else if(cur_page + 2 >= page){
						start_num = page - 4;
						page_group[cur_page+5-page].attr("class","btn_page select");
					}
					for(let i=0;i<5;i++){
						page_group[i].html((start_num+i)+"");
						page_group[i].unbind();
						page_group[i].click(function(res){
							if(index != start_num + i - 1){
								index = start_num + i - 1;
								get_list(index);
							}
						})
					}
				}else{
					for(let i=0;i<page;i++){
						page_group[i].attr("class","btn_page");
						page_group[i].html((i+1)+"");
						page_group[i].unbind();
						page_group[i].click(function(res){
							if(index != i){
								index = i;
								get_list(index);
							}
						})
					}
					for(let i=page;i<5;i++){
						page_group[i].attr("class","btn_page none");
					}
					if(cur_page >= page || cur_page < 0){
						page_group[0].attr("class","btn_page select");
						index = 0;
					}else{
						page_group[cur_page].attr("class","btn_page select");
					}
				}
				
				order_number.html(order_sum);
				page_number.html(page);
				
				for(x in order_list){
					let list_item = templet.clone();
					let order_index = list_item.find("#order_index");
					let id = list_item.find("#id");
					let name = list_item.find("#name");
					let circuit_number = list_item.find("#circuit_number");
					let remark = list_item.find("#remark");
					let start_time = list_item.find("#start_time");
					let step = list_item.find("#step");
					let btn_view = list_item.find("#view");
					let btn_edit = list_item.find("#edit");
					let btn_delete = list_item.find("#delete");
					
					id.attr("id","id"+x);
					btn_view.attr("id","view"+x);
					btn_edit.attr("id","edit"+x);
					btn_delete.attr("id","delete"+x);
					
					list_item.attr("id","order_" + (parseInt(x)+1));
					order_index.html(parseInt(x)+1);
					id.html(order_list[x].id);
					// id.click(function(){
						// window.location.href = "edit.php?id="+id.html()+"&view=true";
					// })
					name.html(order_list[x].name);
					circuit_number.html(order_list[x].circuit_number);
					
					remark.html(order_list[x].remark);
					
					start_time.html(order_list[x].start_time);
					step.html(order_list[x].step);
					
					btn_view.click(function(){
						window.open("edit.php?id="+id.html()+"&view=true");
						//window.location.href = "edit.php?id="+id.html()+"&view=true";
					});
					
					btn_edit.click(function(){
						window.open("edit.php?id="+id.html());
						//window.location.href = "edit.php?id="+id.html();
					});
					
					btn_delete.click(function(){
						if(sql_step == "已撤销"){
							if(confirm("确定删除该工单？")){
								$.ajax({
									type:"POST",
									data:{
										id:id.html(),
									},
									url:"./scripts/delete.php",
									timeout: 5000,
									beforeSend:function(){
										
									},
									error:function(e){
										alert(e);
										console.log(e);
									},
									success:function(data){
										if(data == "success"){
											$.ajax({
												type: "POST",
												data: {
													order_id: id.html(),
												},
												url: "./scripts/delete_process_by_order_id.php",
												timeout: 5000,
												beforeSend: function () {
												},
												error: function (e) {
													alert(e);
													console.log(e);
												},
												success: function(data){
													if(data == "success"){
														location.reload();
													}else{
														alert(data);
													}
												}
											});
										}else{
											alert(data);
										}
									}
								});
							}
						}else{
							if(confirm("确定撤销该工单？")){
								$.ajax({
									type: "POST",
									data: {
										id: id.html(),
										step: '已撤销'
									},
									url: "./scripts/update.php",
									timeout: 5000,
									beforeSend: function () {
									},
									error: function (e) {
										alert(e);
										console.log(e);
									},
									success: function(data){
										if(data == "success"){
											location.reload();
										}else{
											alert(data);
										}
									}
								});
							}
						}
					});
					parent.append(list_item);
				}
			}
			function get_list(page = 0,callback = null,step = sql_step){
				if(callback == null){
					callback = function(data){
						result = data;
						refresh_list(page);
					}
				}
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
						index:page*$("#limit").val(),
						limit:$("#limit").val(),
						step:step
					},
					url:"./scripts/getList.php",
					dataType: 'json',
					timeout: 5000,
					beforeSend:function(){
						
					},
					error:function(e){
						alert(e);
						console.log(e);
					},
					success:callback
				});
			}
			
			function exportBegin(){
				$("#btn_show").css("display","none");
				$("#btn_search").css("display","none");
				$("#btn_empty").css("display","none");
				$("#list_head").css("display","none");
				$("#order_list").css("display","none");
				$("#list_foot").css("display","none");
				
				$("#btn_export").css("display","");
				$("#search_container").attr("class","export");
				//sql_step = "结单";
			}
			
			function exportEnd(){
				$("#btn_show").css("display","");
				$("#btn_show").css("transform","rotate(0)");
				$("#btn_search").css("display","");
				$("#btn_search").attr("class","btn_img hidden");
				$("#btn_empty").css("display","");
				$("#btn_empty").attr("class","btn_img hidden");
				$("#list_head").css("display","");
				$("#order_list").css("display","");
				$("#order_list").attr("class","");
				$("#list_foot").css("display","");
				
				$("#id_input").val("");
				$("#name_input").val("");
				$("#start_time_start").val("");
				$("#start_time_end").val("");
				$("#end_time_start").val("");
				$("#end_time_end").val("");
				$("#number_input").val("");
				
				$("#btn_export").css("display","none");
				$("#search_container").attr("class","hidden");
			}
			
			$("#btn_show").click(function(){
				if(is_search){
					$("#search_container").attr("class","hidden");
					$("#btn_search").attr("class","btn_img hidden");
					$("#btn_empty").attr("class","btn_img hidden");
					$("#btn_show").css("transform","rotate(0)");
					$("#order_list").attr("class","");
				}else{
					$("#search_container").attr("class","");
					$("#btn_search").attr("class","btn_img");
					$("#btn_empty").attr("class","btn_img");
					$("#btn_show").css("transform","rotate(-180deg)");
					$("#order_list").attr("class","short");
				}
				is_search = !is_search;
			})
			
			$("#btn_search").click(function(){
				get_list();
			});
			
			$("#btn_empty").click(function(){
				$("#id_input").val("");
				$("#name_input").val("");
				$("#start_time_start").val("");
				$("#start_time_end").val("");
				$("#end_time_start").val("");
				$("#end_time_end").val("");
				$("#number_input").val("");
			})
			
			$("#start_time_start").jeDate({
				format:"YYYY-MM-DD hh:mm:ss",
				skinCell:"jedatered",
				choosefun:function(elem,datas){
					console.log(datas);
				}
			});
			
			$("#start_time_end").jeDate({
				format:"YYYY-MM-DD hh:mm:ss",
				skinCell:"jedatered",
				choosefun:function(elem,datas){
					console.log(datas);
				}
			});
			
			$("#end_time_start").jeDate({
				format:"YYYY-MM-DD hh:mm:ss",
				skinCell:"jedatered",
				choosefun:function(elem,datas){
					console.log(datas);
				}
			});
			
			$("#end_time_end").jeDate({
				format:"YYYY-MM-DD hh:mm:ss",
				skinCell:"jedatered",
				choosefun:function(elem,datas){
					console.log(datas);
				}
			});
			
			$("#btn_refresh").click(function(){
				get_list(index);
			});
			
			$("#btn_new").click(function(){
				window.open("edit.php");
			});
			
			$("#bar_not_finish").click(function(){
				exportEnd();
				$(".bar_item").attr("class","bar_item");
				$("#bar_not_finish").attr("class","bar_item select");
				sql_step = "未结单|挂起中";
				get_list();
			});
			
			$("#bar_all").click(function(){
				exportEnd();
				$(".bar_item").attr("class","bar_item");
				$("#bar_all").attr("class","bar_item select");
				sql_step = "";
				get_list();
			});
			
			$("#bar_finish").click(function(){
				exportEnd();
				$(".bar_item").attr("class","bar_item");
				$("#bar_finish").attr("class","bar_item select");
				sql_step = "结单";
				get_list();
			});
			
			$("#bar_unfinish").click(function(){
				exportEnd();
				$(".bar_item").attr("class","bar_item");
				$("#bar_unfinish").attr("class","bar_item select");
				sql_step = "未结单";
				get_list();
			});
			
			$("#bar_suspend").click(function(){
				exportEnd();
				$(".bar_item").attr("class","bar_item");
				$("#bar_suspend").attr("class","bar_item select");
				sql_step = "挂起中";
				get_list();
			});
			
			$("#bar_cancel").click(function(){
				exportEnd();
				$(".bar_item").attr("class","bar_item");
				$("#bar_cancel").attr("class","bar_item select");
				sql_step = "已撤销";
				get_list();
			});
			
			$("#bar_export").click(function(){
				exportBegin();
				$(".bar_item").attr("class","bar_item");
				$("#bar_export").attr("class","bar_item select");
			});
			
			$("#bar_customer_query").click(function(){
				window.open("customer_query/index.html");
			});

			$("#bar_charts").click(function(){
				window.open("customer_assess/index.html");
			});
			
			$("#btn_export").click(function(){
				if($("#start_time_start").val() == "" && $("#start_time_end").val()== "" && $("#end_time_start").val() == "" && $("#end_time_end").val() == ""){
					alert("请至少填写一个时间！");
					return;
				}
				let step = "";
				if(!($("#step_complete")[0].checked && $("#step_incomplete")[0].checked && $("#step_suspend")[0].checked && $("#step_cancel")[0].checked)){
					if($("#step_complete")[0].checked){
						step = step + "结单|";
					}
					if($("#step_incomplete")[0].checked){
						step = step + "未结单|";
					}
					if($("#step_suspend")[0].checked){
						step = step + "挂起中|";
					}
					if($("#step_cancel")[0].checked){
						step = step + "已撤销|";
					}
					if(step == ""){
						alert("请至少选择一个工单状态！")
						return;
					}
					step = step.slice(0,-1);
				}
				get_list(0,function(data){
					if(data.sum == 0){
						alert("记录为空！");
					}else if(confirm("查询到"+data.sum+"条记录，确认导出？")){
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
								index:0,
								limit:data.sum,
								step:step
							},
							url:"./scripts/export.php",
							dataType: 'json',
							beforeSend:function(){
								
							},
							error:function(e){
								if(e.statusText == "timeout"){
									alert("请求超时!");
								}else{
									alert(e.statusText);
								}
								console.log(e.statusText);
							},
							success:function(data){
								
								if(data.status == "success"){
									window.open("files/"+data.fileName);
								}else{
									alert(data.error_message);
									console.log(data.error_message);
								}
								
							}
						});
					}
				},step)
			})
			
			$("#to_first").click(function(){
				if(index != 0){
					get_list(0);
					index = 0;
				}
			})
			
			$("#to_pre").click(function(){
				if(index > 0){
					get_list(index - 1);
					index = index - 1;
				}
			})
			
			$("#to_next").click(function(){
				if(index + 1 < parseInt(page_number.html())){
					get_list(index + 1);
					index = index + 1;
				}
			})
			
			$("#to_last").click(function(){
				if(index != parseInt(page_number.html()) - 1){
					get_list(parseInt(page_number.html()) - 1);
					index = parseInt(page_number.html()) - 1;
				}
			})
			
			get_list();
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
			user-select:none;
		}
		
		.bar_item{
			background: #FFFFFF;
			height:5vh;
			font-size:2vh;
			text-align:center;
			line-height:5vh;
			z-index:0;
			transition: all 0.2s;
			user-select:none;
		}
		
		.bar_item:hover{
			position:relative;
			box-shadow:0 0 20px 0 #AAAAAA;
			z-index:3;
		}
		
		.bar_item:active{
			background: #999999;
			color: #EA7777
		}
		
		.bar_item.select{
			color:#FFFFFF;
			background: #EA7777;
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
			height: 29vh;
		}
		
		#search_container.hidden{
			height: 0;
		}
		
		#search_container.export{
			height:35vh;
		}
		
		.search{
			display:flex;
			align-items: center;
			height: 6vh;
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
		
		.checkbox_title{
			display: inline-block;
			font-size:1vh;
			height:1vh;
			line-height:1vh;
			text-align:center;
			margin-right:1vw;
		}
		
		.input {
			display: inline-block;
			width: 20vw;
			height: 4vh;
			line-height:4vh;
			background: #FFFFFF;
			border: 1px solid #EAEAEA;
			padding-left:1vw;
			padding-right:1vw;
		}
		
		#btn_show{
			margin-left: 1vw;
			margin-top: 2vh;
			transition: all 0.5s;
		}
			
	
		#btn_search.hidden{
			width:0;
			padding: 0;
		}
		
		
		#btn_empty.hidden{
			width:0;
			padding: 0;
		}		
		
		#order_list{
			height: 76vh;
			margin:0;
			padding:0;
			overflow-y:auto;
			overflow-x:hidden;
			transition: height 0.5s;
		}
		
		#order_list.short{
			height:47vh;
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
			font-size:0.5vw;
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
		
		.btn{
			font-size:20px;
			margin:2vw;
			display:flex;
			justify-content:center;
			align-items:center;
			border-radius: 1vh;
			background:#EA7777;
			color: #FFFFFF;
			user-select:none;
		}
		
		.btn:hover{
			background:#D96666;
		}
		
		.btn:active{
			background:#C85555;
		}
		
		.btn_img{
			height: 2vh;
			width: 2vh;
			border-radius: 1vh;
			background: #EA7777;
			padding:0.5vh;
			margin: 0.2vh;
			transition: all 0.5s;
			user-select:none;
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
			user-select:none;
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
		
		.btn_page.none{
			display:none;
		}
		
	</style>
</head>
<body>
	<div id="title">政企网络服务中台本地故障单管理系统</div>
	<div id="navigate_bar">
			<div class="bar_head"><b>导航栏</b></div>
			<div id="bar_not_finish" class="bar_item select">在途工单</div>
			<div id="bar_all" class="bar_item">所有工单</div>
			<div id="bar_finish" class="bar_item">已结单</div>
			<div id="bar_unfinish" class="bar_item">未结单</div>
			<div id="bar_suspend" class="bar_item">挂起工单</div>
			<div id="bar_cancel" class="bar_item">已撤单</div>
			<div id="bar_export" class="bar_item">工单导出</div>
			<div class="bar_head"><b>工具箱</b></div>
			<div id="bar_customer_query" class="bar_item">客户查询</div>
			<div id="bar_charts" class="bar_item">统计图表</div>
	</div>
	<div id="container">
		<div id="search_container" class="hidden">
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
			<div id="step" class="search">
				<span class="search_title">工单状态:</span>
				<input id="step_complete" type="checkbox" checked/><span class="checkbox_title">结单</span>
				<input id="step_incomplete" type="checkbox" checked/><span class="checkbox_title">未结单</span>
				<input id="step_suspend" type="checkbox" checked/><span class="checkbox_title">挂起</span>
				<input id="step_cancel" type="checkbox" checked/><span class="checkbox_title">已撤销</span>
			</div>
		</div>
		<div id="btn_export" style="display:none" class="btn">导出</div>
		<img id="btn_show" class="btn_img" src="img/show.png"/>
		<img id="btn_search" class="btn_img hidden" src="img/search.png"/>
		<img id="btn_empty" class="btn_img hidden" src="img/empty.png"/>
		<div id="list_head">
			<span class="index item head"><span class="content">序号</span></span>
			<span class="item head"><span class="content">故障单编号</span></span>
			<span class="item head"><span class="content">客户名称</span></span>
			<span class="item head"><span class="content">电路编号</span></span>
			<span class="item head"><span class="content">备注</span></span>
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
					<span class="item"><span id="remark" class="content">不通</span></span>
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