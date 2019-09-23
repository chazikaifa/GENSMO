<?php
header("Content-Type: text/html;charset=utf-8");

if(isset($_POST['id'])){
	$id = $_POST['id'];
}else{
	die("param error!");
}

$dbhost = 'localhost';  // mysql服务器主机地址
$dbuser = 'gensmo';            // mysql用户名
$dbpass = 'SoSF701TmkYrGY8m';          // mysql用户名密码
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
	die("connect error!");
}
mysqli_query($conn , "set names utf8");
mysqli_select_db($conn,'GENSMO');

$sql = "SELECT * FROM `order` WHERE `id` LIKE '$id'";
$result = mysqli_query($conn, $sql);

if(!$result){
	//$message = array("status" => "error","result" => mysqli_error());
	die(mysqli_error($conn));
}else{
	if($row = mysqli_fetch_row($result)){
		$row["status"] = "success";
		echo json_encode($row);
	}else{
		echo json_encode(array("status" => "fail","error_msg" => "no data!"));
	}
}