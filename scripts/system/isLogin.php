<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

include('../system/DB.php');


if(isset($_POST['token'])){
	$token = $_POST['token'];
}else{
	exit(json_encode(array("status" => "error","errMsg" => 'param error!')));
}

$conn = LinkDB();
if(! $conn ){
	exit(json_encode(array("status" => "error","result" => "mysql connect error: ".mysqli_error($conn))));
}
$sql = "SELECT name FROM `user` WHERE `token` LIKE '$token' AND (`token_time`='2020-01-01 00:00:00' OR `token_time` > CURRENT_TIMESTAMP)";

$result = mysqli_query($conn, $sql);
if($result){
	$row = mysqli_fetch_array($result);
	if($row){
		$res = array("status" => "success","login" => '1',"name" => $row['name']);
		echo json_encode($res);
	}else{
		$res = array("status" => "success","login" => '0');
		echo json_encode($res);
	}
}else{
	$res = array("status" => "error","errMsg" => 'SQL fail');
	echo json_encode($res);
}
?>