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
			var canJump = true;
			function adjust_textarea(obj){
				obj.style.height = 'auto';
				obj.style.height = (obj.scrollHeight) + 'px';
			}
			
			function cal_time(end_time){
				if(end_time != ""){
					let offset = new Date(end_time).getTime() - new Date($("#start_time").val()).getTime();
					offset = offset/1000/60;
					$("#time").val(offset.toFixed(2));
				}
			}
			
			function change_trouble_reason(trouble_class){
				$("#trouble_parent").empty();
				$("#trouble_parent").append("<select id=\"trouble_reason\"><option value=\"\">请选择</option></select>");
				if(trouble_class == "动力配套"){
					$("#trouble_reason").append("<option value=\"电力系统\">电力系统</option>");
					$("#trouble_reason").append("<option value=\"制冷系统\">制冷系统</option>");
				}else if(trouble_class == "设备故障"){
					$("#trouble_reason").append("<option value=\"传输设备\">传输设备</option>");
					$("#trouble_reason").append("<option value=\"交换设备\">交换设备</option>");
					$("#trouble_reason").append("<option value=\"数据设备\">数据设备</option>");
					$("#trouble_reason").append("<option value=\"接入设备\">接入设备</option>");
					$("#trouble_reason").append("<option value=\"客户设备\">客户设备</option>");
					$("#trouble_reason").append("<option value=\"客户端联通设备\">客户端联通设备</option>");
				}else if(trouble_class == "光缆故障"){
					$("#trouble_reason").append("<option value=\"市政施工\">市政施工</option>");
					$("#trouble_reason").append("<option value=\"河涌整治\">河涌整治</option>");
					$("#trouble_reason").append("<option value=\"恶意剪线\">恶意剪线</option>");
					$("#trouble_reason").append("<option value=\"车辆挂断\">车辆挂断</option>");
					$("#trouble_reason").append("<option value=\"老鼠咬断\">老鼠咬断</option>");
					$("#trouble_reason").append("<option value=\"自然灾害\">自然灾害</option>");
					$("#trouble_reason").append("<option value=\"光纤劣化\">光纤劣化</option>");
					$("#trouble_reason").append("<option value=\"尾纤松动\">尾纤松动</option>");
				}else if(trouble_class == "电缆故障"){
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
			
			$('textarea').each(function () {
				this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
			}).on('input', function () {
				adjust_textarea(this);
			});
									
			$("select").selectOrDie({
				onChange: function(){
					if($(this).attr("id") == "trouble_class"){
						change_trouble_reason($(this).val());
					}else if($(this).attr("id") == "step"){
						if($(this).val() == "结单"||$(this).val() == "已撤销"){
							$("#end_time").removeAttr("disabled");
						}else if($(this).val() == "未结单"){
							$("#end_time").attr("disabled","");
						}
					}
				}
			});
			function GetQueryString(name)
			{
				var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
				var r = window.location.search.substr(1).match(reg);
				if(r!=null)
					return  unescape(r[2]); 
				return null;
			}
			if(GetQueryString("id")==null){
				//新建工单
				$("#name").removeAttr("disabled");
				$("#start_time").removeAttr("disabled");
				$("#trouble_symptom").removeAttr("disabled");
				
				$("#start_time").jeDate({
					format:"YYYY-MM-DD hh:mm:ss",
					skinCell:"jedatered",
					choosefun:function(elem,datas){
						console.log(datas)
					}
				});
			}else {
				var id = GetQueryString("id");
				//编辑工单	
				$.ajax({
					type:"POST",
					data:{
						id:id,
					},
					url:"./scripts/getOrderById.php",
					dataType: 'json',
					timeout: 5000,
					beforeSend:function(){
						
					},
					error:function(e){
						alert(e.responseText);
					},
					success:function(data){
						//console.log(data);
						if(data.status == "fail"){
							alert(data.error_msg);
						}else if(data.status == "success"){
							//console.log(data);
							
							$("#id").val(data[0]);
							$("#name").val(data[1]);
							$("#start_time").val(data[2]);
							$("#end_time").val(data[3]);

							$("#time").val(data[4]);
							
							$("#step").val(data[5]);
							
							$("#trouble_symptom").val(data[6]);
							$("#link_id").val(data[7]);
							$("#process").html(data[8]);
							$("#circuit_number").val(data[9]);
							$("#contact_number").val(data[10]);
							$("#contact_name").val(data[11]);
							$("#area").val(data[12]);
							
							if(data[13] == "0" || data[13] == "1"){
								$("#is_trouble").val(data[13]);
							}else {
								$("#is_trouble").val("");
							}
							if(data[14] == "0" || data[14] == "1"){
								$("#is_remote").val(data[14]);
							}else {
								$("#is_remote").val("");
							}
							
							$("#trouble_class").val(data[15]);
							change_trouble_reason(data[15])
							$("#trouble_reason").val(data[16]);
							
							$("#business_type").val(data[17]);
							$("#remark").html(data[18]);
							
							$("#end_time").jeDate({
								format:"YYYY-MM-DD hh:mm:ss",
								skinCell:"jedatered",
								minDate:$("#start_time").val(),
								choosefun:function(elem,datas){
									cal_time(datas);
								},
								okfun:function(elem,datas){
									cal_time(datas);
								}
							});
							
							if(data[5] == "结单"||data[5] == "已撤销"){
								$("#end_time").removeAttr("disabled");
							}else if(data[5] == "未结单"){
								$("#end_time").attr("disabled","");
							}
																				
							$('textarea').each(function () {
								adjust_textarea(this);
							});
							
							cal_time($("#end_time").val());
							
							$("select").selectOrDie("update");
							
							if(GetQueryString("view")=="true"){
								$("select").selectOrDie("disable");
								$(".item").find("input").attr("disabled","");
								$(".item").find("textarea").attr("disabled","");
								$("#btn_confirm").html("编辑");
							}
							
						}else{
							alert("未知错误！")
						}
					}
				});
				

			}
			
			$("#btn_cancel").click(function(){
				window.location.href = "index.php";
			})
			
			$("#btn_confirm").click(function(){
				if($("#name").val() == ""){
					alert("请输入客户名称！");
				}else if($("#start_time").val() == ""){
					alert("请输入故障开始时间！");
				}else{
					if($("#id").val()==""){
						//new
						if(canJump == true){
							$.ajax({
								type:"POST",
								data:{
									name:$("#name").val(),
									start_time:$("#start_time").val(),
									end_time:$("#end_time").val(),
									time:$("#time").val(),
									step:$("#step").val(),
									trouble_symptom:$("#trouble_symptom").val(),
									link_id:$("#link_id").val(),
									process:$("#process").val(),
									circuit_number:$("#circuit_number").val(),
									contact_number:$("#contact_number").val(),
									contact_name:$("#contact_name").val(),
									area:$("#area").val(),
									is_trouble:$("#is_trouble").val(),
									is_remote:$("#is_remote").val(),
									trouble_class:$("#trouble_class").val(),
									trouble_reason:$("#trouble_reason").val(),
									business_type:$("#business_type").val(),
									remark:$("#remark").val(),
								},
								url:"./scripts/new.php",
								timeout: 5000,
								beforeSend:function(){
									canJump = false;
								},
								error:function(e){
									alert(e.responseText);
									canJump = true;
								},
								success:function(data){
									if(data == "success"){
										$("#btn_confirm").html("新建成功！");
										setTimeout(function(){
											window.location.href = "index.php";
										},1000);										
									}else{
										canJump = true;
										alert(data);
									}
								}
							});
						}
					}else{
						//view
						if(GetQueryString("view")=="true"){
							window.location.replace("edit.php?id="+$("#id").val());
						}else if($("#step").val()=="结单" && $("#end_time").val() == ""){
							alert("请输入恢复时间！");
						}else if(canJump == true){
							//update
							$.ajax({
								type:"POST",
								data:{
									id:$("#id").val(),
									name:$("#name").val(),
									start_time:$("#start_time").val(),
									end_time:$("#end_time").val(),
									time:$("#time").val(),
									step:$("#step").val(),
									trouble_symptom:$("#trouble_symptom").val(),
									link_id:$("#link_id").val(),
									process:$("#process").val(),
									circuit_number:$("#circuit_number").val(),
									contact_number:$("#contact_number").val(),
									contact_name:$("#contact_name").val(),
									area:$("#area").val(),
									is_trouble:$("#is_trouble").val(),
									is_remote:$("#is_remote").val(),
									trouble_class:$("#trouble_class").val(),
									trouble_reason:$("#trouble_reason").val(),
									business_type:$("#business_type").val(),
									remark:$("#remark").val(),
								},
								url:"./scripts/update.php",
								timeout: 5000,
								beforeSend:function(){
									canJump = false;
								},
								error:function(e){
									alert(e.responseText);
									canJump = true;
								},
								success:function(data){
									if(data == "success"){
										$("#btn_confirm").html("更新成功！");
										setTimeout(function(){
											window.location.replace("index.php");
										},1000);										
									}else{
										canJump = true;
										alert(data);
									}
								}
							});
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
		
		.sod_select {
			width: -webkit-fill-available;
			height: 40px;
		}
		
		.sod_select:before{
			right:36px;
		}
		
		.sod_select:after{
			top:13px;
		}
		
		.item .sod_list{
			width:47.4vw;
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
			display:flex;
			justify-content:center;
			align-items:center;
			background:#CDCDCD;
			color:#000000;
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
			<div class="item textarea">
				<div class="key">故障过程</div>
				<div class="value textarea">
					<textarea id="process" ></textarea>
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