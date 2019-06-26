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
		$(document).ready(function () {
			var canJump = true;
			var processList = new Array();
			// var suspendList = new Array();
			var show_template = $("#show_template").clone();
			var edit_template = $("#edit_template").clone();
			var add_template = $("#add_template").clone();
			// var suspend_template = $("#suspend_template").clone();
			// var btn_add_suspend = $("#add_suspend").clone();
			var textarea_template = $("#textarea_template").clone();
			var div_template = $("#div_template").clone();
			function adjust_textarea(obj) {
				obj.style.height = 'auto';
				obj.style.height = (obj.scrollHeight) + 'px';
			}

			function cal_time(end_time = $("#end_time").val()) {
				if(is_suspend_available()){
					let suspend_time = get_suspend_time();
					if(suspend_time >= 0){
						if (end_time != "") {
							let offset = new Date(end_time).getTime() - new Date($("#start_time").val()).getTime() - suspend_time;
							offset = offset / 1000 / 60;
							$("#time").val(offset.toFixed(2));
						}else{
							$("#time").val("");
						}
					}
				}else{
					$("#time").val(-2);
				}
				
			}

			function change_trouble_reason(trouble_class) {
				$("#trouble_parent").empty();
				$("#trouble_parent").append("<select id=\"trouble_reason\"><option value=\"\">请选择</option></select>");
				if (trouble_class == "动力配套") {
					$("#trouble_reason").append("<option value=\"机房停电\">机房停电</option>");
					$("#trouble_reason").append("<option value=\"机房电池\">机房电池</option>");
					$("#trouble_reason").append("<option value=\"机房空调\">机房空调</option>");
					$("#trouble_reason").append("<option value=\"基站停电\">基站停电</option>");
					$("#trouble_reason").append("<option value=\"基站电池\">基站电池</option>");
					$("#trouble_reason").append("<option value=\"基站空调\">基站空调</option>");
					$("#trouble_reason").append("<option value=\"室分停电\">室分停电</option>");
					$("#trouble_reason").append("<option value=\"室分电池\">室分电池</option>");
					$("#trouble_reason").append("<option value=\"室分空调\">室分空调</option>");
				} else if (trouble_class == "设备故障") {
					$("#trouble_reason").append("<option value=\"传输设备\">传输设备</option>");
					$("#trouble_reason").append("<option value=\"交换设备\">交换设备</option>");
					$("#trouble_reason").append("<option value=\"数据设备\">数据设备</option>");
					$("#trouble_reason").append("<option value=\"接入设备\">接入设备</option>");
					$("#trouble_reason").append("<option value=\"客户设备\">客户设备</option>");
					$("#trouble_reason").append("<option value=\"客户端联通设备\">客户端联通设备</option>");
				} else if (trouble_class == "光缆故障") {
					$("#trouble_reason").append("<option value=\"市政施工\">市政施工</option>");
					$("#trouble_reason").append("<option value=\"河涌整治\">河涌整治</option>");
					$("#trouble_reason").append("<option value=\"恶意剪线\">恶意剪线</option>");
					$("#trouble_reason").append("<option value=\"车辆挂断\">车辆挂断</option>");
					$("#trouble_reason").append("<option value=\"老鼠咬断\">老鼠咬断</option>");
					$("#trouble_reason").append("<option value=\"自然灾害\">自然灾害</option>");
					$("#trouble_reason").append("<option value=\"光纤劣化\">光纤劣化</option>");
					$("#trouble_reason").append("<option value=\"尾纤松动\">尾纤松动</option>");
				} else if (trouble_class == "电缆故障") {
					$("#trouble_reason").append("<option value=\"市政施工\">市政施工</option>");
					$("#trouble_reason").append("<option value=\"河涌整治\">河涌整治</option>");
					$("#trouble_reason").append("<option value=\"恶意剪线\">恶意剪线</option>");
					$("#trouble_reason").append("<option value=\"车辆挂断\">车辆挂断</option>");
					$("#trouble_reason").append("<option value=\"老鼠咬断\">老鼠咬断</option>");
					$("#trouble_reason").append("<option value=\"自然灾害\">自然灾害</option>");
					$("#trouble_reason").append("<option value=\"光纤劣化\">电缆劣化</option>");
					$("#trouble_reason").append("<option value=\"尾纤松动\">电缆松动</option>");
				}
					$("#trouble_reason").selectOrDie();
			}

			function show_to_edit(obj) {
				let textarea = textarea_template.clone();

				textarea.attr("id", "");
				textarea.html($(obj).html());

				obj.replaceWith(textarea);
				
				textarea.each(function () {
					this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
				}).on('input', function () {
					adjust_textarea(this);
				}).blur(function () {
					processList[parseInt($(this).parent().attr("index"))].description = $(this).val();
					//console.log(processList);
					edit_to_show($(this));
				});
				textarea.focus();
			}

			function edit_to_show(obj) {
				let div = div_template.clone();
				
				div.attr("id","");
				div.html($(obj).val());
				if (GetQueryString("view") != "true") {
					div.click(function(){
						show_to_edit($(this));
					})
				}
				obj.replaceWith(div);
			}
			
			function get_process(){
				$.ajax({
					type: "POST",
					dataType: 'json',
					data: {
						order_id: GetQueryString("id"),
					},
					url: "./scripts/get_process_list_by_order_id.php",
					timeout: 5000,
					beforeSend: function () {
						canJump = false;
					},
					error: function (e) {
						alert(e.responseText);
						canJump = true;
					},
					success: function (data) {
						canJump = true;
						if (data.status == "success") {
							processList = data.result;
							console.log(processList);
							refresh_process_list();
						} else {
							alert(data.error_message);
						}
					}
				});
			}
			
			function refresh_process_list(){
				$("#process_list").empty();
				if(processList.length <= 0){
					processList[0] = {
						process_id: 'new_process',
						order_id: $("#id").val(),
						description: '',
						list_order: 0,
						time: ""
					};
				}
				for (x in processList) {
					let template = show_template.clone();
					let show_index = template.find("#index");
					let show_mark = template.find("#mark");
					let show_time = template.find("#time");
					let content = template.find("#div_template");
					let btn_minus = template.find("#btn_minus");

					template.attr("id", processList[x].process_id);
					template.attr("index", parseInt(x));
					show_index.html((parseInt(x) + 1));
					
					show_mark.val(processList[x].mark);
					
					show_time.val(processList[x].time);
					show_time.jeDate({
						format: "YYYY-MM-DD hh:mm:ss",
						skinCell: "jedatered",
						minDate: $("#start_time").val(),
						choosefun: function (elem, datas) {
							processList[parseInt(template.attr("index"))].time = show_time.val();
							cal_time();
						},
						okfun: function (elem, datas) {
							processList[parseInt(template.attr("index"))].time = show_time.val();
							cal_time();
						}
					});
					content.attr("id", "");
					content.html(processList[x].description);
					content.click(function () {
						if (GetQueryString("view") != "true") {
							show_to_edit($(this) ,$(this).html());
						}
					})
					if (GetQueryString("view") != "true") {
						btn_minus.click(function () {
							if (confirm("确定删除这一条进展？")) {
								processList.splice(parseInt(template.attr("index")), 1);
								refresh_process_list();
							}
						});
					}else{
						btn_minus.attr("style","display:none");
						content.attr("class","process_content long");
						show_mark.selectOrDie("disable");
						show_time.attr("disabled","");
					}
					
					show_mark.selectOrDie({
						onChange: function () {
							processList[parseInt(template.attr("index"))].mark = $(this).val();
						}
					});
					
					$("#process_list").append(template);
				}
				if (GetQueryString("view") != "true") {
					let add = add_template.clone();
					$("#process_list").append(add);
					add.click(function(){
						processList.push({
							process_id: "new_process",
							order_id: $("#id").val(),
							description: "",
							time: "",
							list_order:processList.length+""
						});
						console.log(processList);
						refresh_process_list();
					});
				}
			}
			
			function delete_process(callback){
				$.ajax({
					type: "POST",
					data: {
						order_id: $("#id").val(),
					},
					url: "./scripts/delete_process_by_order_id.php",
					timeout: 5000,
					beforeSend: function () {
					},
					error: function (e) {
						alert(e.responseText);
					},
					success: callback
				});
			}
			
			function add_process(process_item,callback){
				$.ajax({
					type: "POST",
					data: {
						process_id: process_item.process_id,
						order_id: process_item.order_id,
						time: process_item.time,
						mark: process_item.mark,
						description: process_item.description,
						list_order: process_item.list_order
					},
					url: "./scripts/add_process.php",
					timeout: 5000,
					beforeSend: function () {
					},
					error: function (e) {
						alert(e.responseText);
					},
					success: callback
				});
			}
			
			function update_process(callback){
				let complete = 0;
				delete_process(function(data){
					for(x in processList){
						add_process(processList[x],function(data){
							if(data == "success"){
								complete++;
								if(complete >= processList.length){
									callback();
								}
							}else{
								console.log(data);
							}
						});
					}
				});
			}
			
			function is_suspend_available(){
				//检查进展合理性:
				//1、进展时间不能为空
				//2、后一条进展时间应比前一条进展晚
				//3、挂起、解挂标志不能互相嵌套（即在解挂前不能进行挂起）
				let last_mark = "unset_suspend";
				let last_time = 0;
				for(x in processList){
					if(processList[x].time == ""){
						return false;
					}else if(new Date(processList[x].time).getTime() - last_time < 0){
						return false;
					}else{
						last_time = new Date(processList[x].time).getTime();
					}
					if(processList[x].mark == ""){
						continue;
					}else{
						if(processList[x].mark == last_mark){
							return false;
						}else{
							last_mark = processList[x].mark;
						}
					}
				}
				return true;
			}
			
			function get_suspend_time(){
				let suspend_time = 0;
				let start_time = -1;
				for(x in processList){
					if(processList[x].mark == "set_suspend"){
						start_time = new Date(processList[x].time).getTime();
					}else if(processList[x].mark == "unset_suspend"){
						if(start_time >= 0){
							suspend_time = suspend_time + new Date(processList[x].time).getTime() - start_time;
							start_time = -1;
						}else{
							//错误，挂起与解挂存在嵌套情况
							//当is_suspend_available()返回true,不会出现这种情况
							return -2;
						}
					}
				}
				if(start_time >= 0){
					//挂起中，未解挂
					return -1;
				}else{
					return suspend_time;
				}
			}
			
			function new_order(){
				$.ajax({
					type: "POST",
					data: {
						name: $("#name").val(),
						start_time: $("#start_time").val(),
						end_time: $("#end_time").val(),
						time: $("#time").val(),
						step: $("#step").val(),
						trouble_symptom: $("#trouble_symptom").val(),
						link_id: $("#link_id").val(),
						process: "该字段已弃用",
						circuit_number: $("#circuit_number").val(),
						contact_number: $("#contact_number").val(),
						contact_name: $("#contact_name").val(),
						area: $("#area").val(),
						is_trouble: $("#is_trouble").val(),
						is_remote: $("#is_remote").val(),
						trouble_class: $("#trouble_class").val(),
						trouble_reason: $("#trouble_reason").val(),
						business_type: $("#business_type").val(),
						remark: $("#remark").val(),
					},
					url: "./scripts/new.php",
					timeout: 5000,
					beforeSend: function () {
						canJump = false;
					},
					error: function (e) {
						alert(e.responseText);
						canJump = true;
					},
					success: function (data) {
						if (data == "success") {
							$("#btn_confirm").html("新建成功！");
							setTimeout(function () {
								window.location.href = "index.php";
							}, 1000);
						} else {
							canJump = true;
							alert(data);
						}
					}
				});
			}
			
			function update_order(){
				if(!is_suspend_available()){
					alert("不合理的故障过程，请检查：\n1、进展时间不能为空\n2、后一条进展时间应比前一条进展晚\n3、挂起、解挂标志不能互相嵌套（即在解挂前不能进行挂起）");
					return;
				}else if(processList.length > 0 && $("#end_time").val() != "" && new Date(processList[processList.length-1].time).getTime() - new Date($("#end_time").val()).getTime() > 0){
					alert("错误！结单时间比最后一条进展的时间早！");
					return;
				}else if(get_suspend_time() == -1 && $("#step").val() != "挂起中"){
					if(confirm("工单状态应为'挂起中',是否自动修改？")){
						$("#step").val("挂起中");
						$("#step").selectOrDie("update");
					}else{
						return;
					}
				}else if(get_suspend_time() != -1 && $("#step").val() == "挂起中"){
					if(confirm("工单当前并无挂起,是否自动修改为'未结单'？")){
						$("#step").val("未结单");
						$("#step").selectOrDie("update");
					}else{
						return;
					}
				}
				update_process(function(){
					$.ajax({
						type: "POST",
						data: {
							id: $("#id").val(),
							name: $("#name").val(),
							start_time: $("#start_time").val(),
							end_time: $("#end_time").val(),
							time: $("#time").val(),
							step: $("#step").val(),
							trouble_symptom: $("#trouble_symptom").val(),
							link_id: $("#link_id").val(),
							process: "该字段已弃用",
							circuit_number: $("#circuit_number").val(),
							contact_number: $("#contact_number").val(),
							contact_name: $("#contact_name").val(),
							area: $("#area").val(),
							is_trouble: $("#is_trouble").val(),
							is_remote: $("#is_remote").val(),
							trouble_class: $("#trouble_class").val(),
							trouble_reason: $("#trouble_reason").val(),
							business_type: $("#business_type").val(),
							remark: $("#remark").val(),
						},
						url: "./scripts/update.php",
						timeout: 5000,
						beforeSend: function () {
							canJump = false;
						},
						error: function (e) {
							alert(e.responseText);
							canJump = true;
						},
						success: function (data) {
							if (data == "success") {
								$("#btn_confirm").html("更新成功！");
								setTimeout(function () {
									window.location.replace("index.php");
								}, 1000);
							} else {
								canJump = true;
								alert(data);
							}
						}
					});
				});
			}
			
			$('textarea').each(function () {
				this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
			}).on('input', function () {
				adjust_textarea(this);
			});

			$("select").selectOrDie({
				onChange: function () {
					if ($(this).attr("id") == "trouble_class") {
						change_trouble_reason($(this).val());
					} else if ($(this).attr("id") == "step") {
						if ($(this).val() == "结单" || $(this).val() == "已撤销") {
							$("#end_time").removeAttr("disabled");
						} else if ($(this).val() == "未结单" || $(this).val() == "挂起中") {
							$("#end_time").attr("disabled", "");
						}
					}
				}
			});
			function GetQueryString(name) {
				var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
				var r = window.location.search.substr(1).match(reg);
				if (r != null)
					return unescape(r[2]);
				return null;
			}
			if (GetQueryString("id") == null) {
				//新建工单
				$("#name").removeAttr("disabled");
				$("#start_time").removeAttr("disabled");
				$("#trouble_symptom").removeAttr("disabled");
					
				$("#step").selectOrDie("disable");
				
				$("#start_time").jeDate({
					format: "YYYY-MM-DD hh:mm:ss",
					skinCell: "jedatered",
					choosefun: function (elem, datas) {
						//console.log(datas)
					}
				});
				
				//新建工单时无工单id，不能创建故障过程
				$("#process").css("display","none");

			} else {
				var id = GetQueryString("id");
				//编辑工单
				$.ajax({
					type: "POST",
					data: {
						id: id,
					},
					url: "./scripts/getOrderById.php",
					dataType: 'json',
					timeout: 5000,
					beforeSend: function () {},
					error: function (e) {
						alert(e.responseText);
					},
					success: function (data) {
						//console.log(data);
						if (data.status == "fail") {
							alert(data.error_msg);
						} else if (data.status == "success") {
							//console.log(data);
							get_process();
							
							$("#id").val(data[0]);
							$("#name").val(data[1]);
							$("#start_time").val(data[2]);
							$("#end_time").val(data[3]);

							$("#time").val(data[4]);

							$("#step").val(data[5]);

							$("#trouble_symptom").val(data[6]);
							$("#link_id").val(data[7]);
														
							$("#circuit_number").val(data[9]);
							$("#contact_number").val(data[10]);
							$("#contact_name").val(data[11]);
							$("#area").val(data[12]);

							if (data[13] == "0" || data[13] == "1") {
								$("#is_trouble").val(data[13]);
							} else {
								$("#is_trouble").val("");
							}
							if (data[14] == "0" || data[14] == "1") {
								$("#is_remote").val(data[14]);
							} else {
								$("#is_remote").val("");
							}

							$("#trouble_class").val(data[15]);
							change_trouble_reason(data[15])
							$("#trouble_reason").val(data[16]);

							$("#business_type").val(data[17]);
							$("#remark").html(data[18]);

							$("#end_time").jeDate({
								format: "YYYY-MM-DD hh:mm:ss",
								skinCell: "jedatered",
								minDate: $("#start_time").val(),
								choosefun: function (elem, datas) {
									cal_time();
								},
								okfun: function (elem, datas) {
									cal_time();
								}
							});

							if (data[5] == "结单" || data[5] == "已撤销") {
								$("#end_time").removeAttr("disabled");
							} else if (data[5] == "未结单" || data[5] == "挂起中") {
								$("#end_time").attr("disabled", "");
							}

							$('textarea').each(function () {
								adjust_textarea(this);
							});

							cal_time();

							$("select").selectOrDie("update");

							if (GetQueryString("view") == "true") {
								$("select").selectOrDie("disable");
								$(".item").find("input").attr("disabled", "");
								$(".item").find("textarea").attr("disabled", "");
								$("#btn_confirm").html("编辑");
							}

						} else {
							alert("接口错误！")
						}
					}
				});
			}

			$("#btn_cancel").click(function () {
				window.location.href = "index.php";
			})

			$("#btn_confirm").click(function () {
				if ($("#name").val() == "") {
					alert("请输入客户名称！");
				} else if ($("#start_time").val() == "") {
					alert("请输入故障开始时间！");
				} else {
					if ($("#id").val() == "") {
						//new
						if (canJump == true) {
							new_order();
						}
					} else {
						//view
						if (GetQueryString("view") == "true") {
							window.location.replace("edit.php?id=" + $("#id").val());
						} else if ($("#step").val() == "结单") {
							if($("#end_time").val() == ""){
								alert("请输入恢复时间！");
							}else if($("#area").val() == ""){
								alert("请选择客户区域！")
							}else if($("#is_trouble").val() == ""){
								alert("请选择是否故障！")
							}else if($("#is_remote").val() == ""){
								alert("请选择是否对端！")
							}else if($("#trouble_class").val() == ""){
								alert("请选择故障分类！")
							}else if($("#trouble_reason").val() == ""){
								alert("请选择原因细化！")
							}else if($("#business_type").val() == ""){
								alert("请选择行业类型！")
							}else if (canJump == true) {
								//update
								update_order();
							}
						} else if(canJump == true){
							//update
							update_order();
						}
					}
				}
			})
		})
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
		#container{
			height:92vh;
			width:100vw;
			overflow: auto;
			display:flex;
			justify-content:center;
			padding-top: 3vh;
			background:#EAEAEA;
		}
		#bg{
			height: fit-content;
			margin-bottom:6vh;
			width: 50vw;
			background-color: #FFFFFF; 
			border-radius: 2vh;
		}
		.item{
			height: 62px;
			width:47.4vw;
			margin-left:1.3vw;
			margin-right:1.3vw;
			margin-top:12px;
		}
		
		.item.half{
			width:22vw;
			margin-left:1.3vw;
			margin-right:1.3vw;
			display:inline-block;
		}
		
		.item.textarea{
			height:fit-content;
		}
		
		.key{
			height:12px;
			font-size:12px;
			color:#EA7777;
		}
		.value{
			height:50px;
			display:flex;
			align-items: center;
		}
		
		.value.textarea{
			min-height:50px;
			height:fit-content;
		}
		
		.value > input {
			padding: 5px;
			height: 35px;
			font-size:15px;
			width: -webkit-fill-available;
		}
		
		.value > textarea {
			padding: 5px;
			margin-top:5px;
			font-size:15px;
			width: -webkit-fill-available;
			resize:none;
		}
		
		.value > .sod_select {
			width: -webkit-fill-available;
			height: 40px;
		}
		
		.value > .sod_select:before{
			right:36px;
		}
		
		.value > .sod_select:after{
			top:13px;
		}
		
		.item .sod_list{
			width:47.4vw;
		}
		
		.item .sod_list_wrapper{
			margin: 0;
			border: none;
		}
		
		.item.half .sod_list{
			width:22vw;
		}
		
		.sod_option{
			width: -webkit-fill-available;
		}
		
		.item .btn{
			width:-webkit-fill-available;
			border-radius:5px;
			height: 30px;
			font-size:15px;
			display:flex;
			justify-content:center;
			align-items:center;
			background:#CDCDCD;
			color:#000000;
		}
		
		#process_list{
			margin: 0;
			padding: 0;
			width:47.4vw;
			height:fit-content;
		}	
	
		.process_item{
			list-style:none;
			height:auto;
			width:-webkit-fill-available;
			display:flex;
			align-items:center;
			margin-top:10px;
		}
		
		.process_item.add{
			height: 30px;
			border-radius:1vh;
			background: #EA7777;
			color: white;
			justify-content: center;
			font-size:15px;
		}
		
		.process_item.add:hover{
			background:#D96666;
		}
		
		.process_item.add:active{
			background:#C85555;
		}
		
		.process_item > textarea{
			display: inline-block;
			min-height: 40px;
			resize:none;
			width:21vw;
			font-size: 20px;
			margin-left:0.5vw;
			margin-right:0.1vw;
			padding-top:5px;
			padding-bottom:5px;
			padding-right:0.5vw;
			padding-left:0.5vw;
		}
		
		/*
		#edit_template{
			display:none;
		}
		
		#show_template{
			display:none;
		}
		*/
		.btn_img{
			height: 2vh;
			width: 2vh;
			border-radius: 2vh;
			background: #EA7777;
			padding:0.5vh;
			margin: 0.2vh;
		}
		
		.process_index{
			display:inline-block;
			width:2vw;
			font-size:20px;
			height:25px;
			color: #EA7777;
		}
		
		.process_item > .sod_select{
			width: 8vw;
			margin-left:0.5vw;
			margin-right:0.5vw;
		}
		
		.process_item .sod_list_wrapper{
			margin: 0;
			border: none;
		}
		
		.process_item .sod_list{
			width: 8vw;
		}
		
		.process_mark{
			display:flex;
			justify-content:center;
			align-items:center;
			width:3vw;
			margin-left:0.5vw;
			margin-right:0.5vw;
			font-size:12px;
			height:25px;
			color: #EA7777;
		}
		
		.process_time{
			width: 12vw;
			height:40px;
			font-size:12px;
			text-align:center;
		}
		
		.process_content{
			min-height:30px;
			display:inline-block;
			width:20vw;
			margin-left:0.5vw;
			font-size:20px;
			height:fit-content;
			padding-right:0.5vw;
			padding-left:0.5vw;
			padding-top:5px;
			padding-bottom:5px;
			border-left: 1px #AAAAAA solid;
			word-wrap:break-word;
		}
		
		.process_content.long{
			width:22vw;
		}
		
		.process_content:hover{
			background:#EAEAEA;
		}
		
		.process_content:active{
			background:#D9D9D9;
		}
		
		#suspend_list{
			margin: 0;
			padding: 0;
			width:47.4vw;
			height:fit-content;
		}	
		
		#suspend_template{
			display:none;
		}
		
		.suspend_item{
			list-style:none;
			height:auto;
			width:-webkit-fill-available;
			display:flex;
			align-items:center;
			margin-top:10px;
		}
		
		.suspend_step{
			margin: 1vw;
			width: 21vw;
			height: 30px;
		}
		
		.btn_img:active{
			background: #D96666;
		}
		
		.btn:hover{
			background:#BCBCBC;
		}
		
		.btn:active{
			background:#AAAAAA;
			color:#FFFFFF;
		}
		
		.btn.primary{
			background:#EA7777;
			color:#FFFFFF;
		}
		
		.btn.primary:hover{
			background:#D96666;
		}
		
		.btn.primary:active{
			background:#C85555;
			color:#000000;
		}
	</style>
</head>
<body>
	<div id="title">政企网络服务中台本地故障单管理系统</div>
	<div id="container">
		<div id="bg">
			<div class="item">
				<div class="key">故障单编号</div>
				<div class="value">
					<input id="id" type="text" value="" disabled />
				</div>
			</div>
			<div class="item">
				<div class="key">*客户名称</div>
				<div class="value">
					<input id="name" type="text" value="" disabled />
				</div>
			</div>
			<div class="item half">
				<div class="key">*开始时间</div>
				<div class="value">
					<input id="start_time" type="text" value="" disabled readonly />
				</div>
			</div>
			<div class="item half">
				<div class="key">恢复时间</div>
				<div class="value">
					<input id="end_time" type="text" value="" disabled readonly />
				</div>
			</div>
			<div class="item half">
				<div class="key">历时(分钟)</div>
				<div class="value">
					<input id="time" type="text" value="" disabled />
				</div>
			</div>
			<div class="item half">
				<div class="key">*工单状态</div>
				<div class="value">
					<select id="step">
						<option value="未结单">未结单</option>
						<option value="结单">结单</option>
						<option value="挂起中">挂起中</option>
						<option value="已撤销">已撤销</option>
				</select>
				</div>
			</div>
			<div class="item">
				<div class="key">故障简述</div>
				<div class="value">
					<input id="trouble_symptom" type="text" value="" />
				</div>
			</div>
			<div class="item half">
				<div class="key">19工单编号</div>
				<div class="value">
					<input id="link_id" type="text" value="" />
				</div>
			</div>
			<div class="item half">
				<div class="key">电路编号</div>
				<div class="value">
					<input id="circuit_number" type="text" value="" />
				</div>
			</div>
			<div class="item half">
				<div class="key">客户联系方式</div>
				<div class="value">
					<input id="contact_number" type="text" value="" />
				</div>
			</div>
			<div class="item half">
				<div class="key">客户姓名</div>
				<div class="value">
					<input id="contact_name" type="text" value="" />
				</div>
			</div>
			<div class="item half">
				<div class="key">公司所在区域</div>
				<div class="value">
					<select id="area">
						<option value="">请选择</option>
						<option value="天河">天河</option>
						<option value="越秀">越秀</option>
						<option value="白云南">白云南</option>
						<option value="白云北">白云北</option>
						<option value="番禺">番禺</option>
						<option value="萝岗">萝岗</option>
						<option value="海珠">海珠</option>
						<option value="花都">花都</option>
						<option value="黄埔">黄埔</option>
						<option value="荔湾">荔湾</option>
						<option value="从化">从化</option>
						<option value="增城">增城</option>
						<option value="南沙">南沙</option>
						<option value="非广州">非广州</option>
					</select>
				</div>
			</div>
			<div class="item half">
				<div class="key">是否故障</div>
				<div class="value">
				<select id="is_trouble">
					<option value="">请选择</option>
					<option value="1">是</option>
					<option value="0">否</option>
				</select>
				</div>
			</div>
			<div class="item half">
				<div class="key">是否对端</div>
				<div class="value">
					<select id="is_remote">
						<option value="">请选择</option>
						<option value="1">是</option>
						<option value="0">否</option>
					</select>
				</div>
			</div>
			<div class="item half">
				<div class="key">故障分类</div>
				<div class="value">
					<select id="trouble_class">
						<option value="">请选择</option>
						<option value="光缆故障">光缆故障</option>
						<option value="设备故障">设备故障</option>
						<option value="动力配套">动力配套</option>
						<option value="电缆故障">电缆故障</option>
					</select>
				</div>
			</div>
			<div class="item half">
				<div class="key">原因细化</div>
				<div id="trouble_parent" class="value">
					<select id="trouble_reason">
						<option value="">请选择</option>
					</select>
				</div>
			</div>
			<div class="item half">
				<div class="key">行业类型</div>
				<div class="value">
					<select id="business_type">
						<option value="">请选择</option>
						<option value="党政军部门">党政军部门</option>
						<option value="金融、保险业">金融、保险业</option>
						<option value="交通运输（含邮政、快递）、仓储业">交通运输（含邮政、快递）、仓储业</option>
						<option value="科学教育、文化卫生">科学教育、文化卫生</option>
						<option value="旅游、饭店、娱乐服务业">旅游、饭店、娱乐服务业</option>
						<option value="通信、电子设备制造和计算机应用服务业">通信、电子设备制造和计算机应用服务业</option>
						<option value="邮电计算机信息业">邮电计算机信息业</option>
						<option value="建筑业">建筑业</option>
						<option value="其他行业">其他行业</option>
					</select>
				</div>
			</div>
			<div class="item textarea" id="process">
				<div class="key">故障过程</div>
				<div class="value textarea">
					<ul id="process_list">
						<li id="edit_template" class="process_item" index="-1" >
							<div id="index" class="process_index">
								1
							</div>
							<textarea id="textarea_template" rows="1"></textarea>
							<img id="btn_minus" class="btn_img" src="img/minus.png"/>
						</li>
						<li id="show_template" class="process_item" index="-1">
							<div id="index" class="process_index">
								2
							</div>
							<select id="mark">
								<option value="set_suspend">挂起</option>
								<option value="unset_suspend">解挂</option>
								<option value="">进展</option>
							</select>
							<input id="time" class="process_time" type="text" value="2019-06-01 09:00:00" readonly />
							<div id="div_template" class="process_content"></div>
							<img id="btn_minus" class="btn_img" src="img/minus.png"/>
						</li>
						<li id="add_template" class="process_item add">
							添加进展
						</li>
					</ul>
				</div>
			</div>
			<div class="item textarea">
				<div class="key">备注</div>
				<div class="value textarea">
					<textarea id="remark" ></textarea>
				</div>
			</div>
			<div class="item half">
				<div id="btn_cancel" class="btn">
					返回
				</div>
			</div>
			<div class="item half">
				<div id="btn_confirm" class="btn primary">
					确认
				</div>
			</div>
		</div>
	</div>
</body>
</html>