<?php
header("Content-Type: text/html;charset=utf-8");

$param_name = array();
$param_name[0] = 'process_id';
$param_name[1] = 'order_id';
$param_name[3] = 'time';
$param_name[4] = 'description';
$param_name[4] = 'list_order';
$param_name[5] = 'mark';

$param = array();

foreach($param_name as $index => $name){
	if(isset($_POST[$name])){
		if($name == 'process_id' && $_POST[$name] == ''){
			die('process_id is empty!')
		}else{
			$param[$name] = $_POST[$name];
		}
	}else{
		if($name == 'process_id'){
			die('process_id is NOT SET!')
		}else{
			array_slice($param_name,$index,1);
		}
	}
}

$dbhost = 'localhost';  // mysql服务器主机地址
$dbuser = 'root';            // mysql用户名
$dbpass = '';          // mysql用户名密码
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
    die('Could not connect: ' . mysqli_error($conn));
}
mysqli_query($conn , "set names utf8");
mysqli_select_db($conn,'GENSMO');

$sql = "UPDATE `process` SET ";

foreach($param_name as $name){
	if($name != 'process_id'){
		$sql .= '`'.$name.'` = ';
		$sql .= "'".$param[$name]."', ";
	}
}
$id = $param['process_id'];
$sql = substr($sql,0,strlen($sql)-2);
$sql .= " WHERE `process_id` = '$id'";

$result = mysqli_query($conn, $sql);
if(!$result){
	die('error:'.mysqli_error($conn));
}else{
	echo "success";
}

mysqli_close($conn);