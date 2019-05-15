<?php
$id = $_POST['id'];
$name = $_POST['name'];
$start_time_start = $_POST['start_time_start'];
$start_time_end = $_POST['start_time_end'];
$end_time_start = $_POST['end_time_start'];
$end_time_end = $_POST['end_time_end'];
$number = $_POST['number'];
$index = $_POST['index'];
$limit = $_POST['limit'];

$dbhost = 'localhost';  // mysql服务器主机地址
$dbuser = 'root';            // mysql用户名
$dbpass = '';          // mysql用户名密码
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
	//$message = array("status" => "error","result" => mysqli_error());
	die(mysqli_error());
}
mysqli_query($conn , "set names utf8");
mysqli_select_db($conn,'GENSMO');

$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM `order` ORDER BY `start_time` DESC LIMIT '.$index.','.$limit ;
$result = mysqli_query($conn, $sql);
if(!$result){
	//$message = array("status" => "error","result" => mysqli_error());
	die(json_encode(mysqli_error()));
}else{
	$row_number = mysqli_fetch_row(mysqli_query($conn,"SELECT FOUND_ROWS()"))[0];
	$resList = array();
	$i = 0;
	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
		$resList[$i] = $row;
		$i++;
	}
	$res = array("status" => "success","sum" => $row_number,"result" => $resList);
	echo json_encode($res);
}
mysqli_close($conn);
?>