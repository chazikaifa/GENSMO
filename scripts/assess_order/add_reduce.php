<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

if(isset($_POST['id'])&&isset($_POST['province'])&&isset($_POST['time'])&&isset($_POST['reduce_time'])&&isset($_POST['time_limit'])){
	$id = $_POST['id'];
	$province = $_POST['province'];
	$time = $_POST['time'];
	$reduce_time = $_POST['reduce_time'];
	$time_limit = (int)$_POST['time_limit'];
	$assess_time = (int)($time) - (int)($reduce_time);
	if($assess_time > $time_limit){
		$timeout = 1;
	}else{
		$timeout = 0;
	}
}else{
	$res = array("status" => 'error','errMsg' => 'data not set');
	exit(json_encode($res));
}

$dbhost = 'localhost';  // mysql服务器主机地址
$dbuser = 'gensmo';            // mysql用户名
$dbpass = 'SoSF701TmkYrGY8m';          // mysql用户名密码
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
	$res = array("status" => 'error','errMsg' => 'mysql error: '.mysqli_error($conn));
	exit(json_encode($res));
}
mysqli_query($conn , "set names utf8");
mysqli_select_db($conn,'GENSMO');

if($province != ''){
	$sql = "UPDATE `assess_order` SET `province`='$province',`reduce_time`=$reduce_time,`assessment_time`=$assess_time,`time_out`=$timeout WHERE `orderID`='$id'";
}else{
	$sql = "UPDATE `assess_order` SET `reduce_time`=$reduce_time,`assessment_time`=$assess_time,`time_out`=$timeout WHERE `orderID`='$id'";
}

$result = mysqli_query($conn, $sql);
if(!$result){
	$res = array("status" => 'error','errMsg' => 'mysql error: '.mysqli_error($conn));
	exit(json_encode($res));
}else{
	$row = mysqli_affected_rows($conn);
	$res = array("status" => "success","row" => $row);
	echo json_encode($res);
}

mysqli_close($conn);

?>