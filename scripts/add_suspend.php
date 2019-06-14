<?php
header("Content-Type: text/html;charset=utf-8");

$param_name = array();
$param_name[0] = 'suspend_id';
$param_name[1] = 'order_id';
$param_name[2] = 'start_time';
$param_name[3] = 'end_time';
$param_name[4] = 'description';

$param = array();

foreach($param_name as $name){
	if(isset($_POST[$name])){
		if(($name == 'start_time'||$name == 'end_time'||$name == 'order_id')&&$_POST[$name] == ''){
			die('param \''.$name.'\' is empty!');
		}else{
			$param[$name] = $_POST[$name];
		}
	}else{
		if($name == 'start_time'||$name == 'end_time'||$name == 'order_id'){
			die('param \''.$name.'\' is NOT set!');
		}else if($name == 'description'|| $name = 'suspend_id'){
			$param[$name] = '';
		}
	}
}

$dbhost = 'localhost';  // mysql服务器主机地址
$dbuser = 'root';            // mysql用户名
$dbpass = '';          // mysql用户名密码
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
	//$message = array("status" => "error","result" => mysqli_error());
	die('Could not connect: ' . mysqli_error($conn));
}
mysqli_query($conn , "set names utf8");
mysqli_select_db($conn,'GENSMO');

$sql = 'INSERT INTO `suspend` (';

foreach($param_name as $name){
	$sql .= '`'.$name.'`,';
}

$sql = substr($sql,0,strlen($sql)-1);
$sql .= ') VALUES (';

foreach($param as $p){
	$sql .= "'".$p."',";
}
$sql = substr($sql,0,strlen($sql)-1);
$sql .= ')';

// echo $sql;

$result = mysqli_query($conn, $sql);
if(!$result){
	die('error:'.mysqli_error($conn));
}else{
	echo "success";
}

mysqli_close($conn);
?>