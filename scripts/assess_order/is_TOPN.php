<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

if(isset($_POST['name'])){
	$name = $_POST['name'];
}else{
	exit(json_encode(array("status" => "error","errMsg" => 'name not set!')));
}


$dbhost = 'localhost';  // mysql服务器主机地址
$dbuser = 'gensmo';            // mysql用户名
$dbpass = 'SoSF701TmkYrGY8m';          // mysql用户名密码
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(! $conn ){
	exit(json_encode(array("status" => "error","result" => "mysql connect error: ".mysqli_error($conn))));
}
mysqli_query($conn , "set names utf8");
mysqli_select_db($conn,'GENSMO');

$sql = "SELECT `level` FROM `assess_customer` WHERE `name` LIKE '$name' OR `unify_name` LIKE '$name'";

$result = mysqli_query($conn, $sql);
if($result){
	$row = mysqli_fetch_array($result);
	if($row){
		$row['status'] = 'success';
		$row['result'] = 'true';
		echo json_encode($row);
	}else{
		$res = array("status" => "success","result" => 'false');
		echo json_encode($res);
	}
}else{
	$res = array("status" => "error","errMsg" => 'SQL fail');
	echo json_encode($res);
}
?>