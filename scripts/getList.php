<?php
header("Content-Type: text/html;charset=utf-8");
$id = $_POST['id'];
$name = $_POST['name'];
$start_time_start = $_POST['start_time_start'];
$start_time_end = $_POST['start_time_end'];
$end_time_start = $_POST['end_time_start'];
$end_time_end = $_POST['end_time_end'];
$number = $_POST['number'];
$index = $_POST['index'];
$limit = $_POST['limit'];
if(isset($_POST['step'])){
	$step = $_POST['step'];
}else{
	$step = "";
}
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

$condition = "";
if($id != ""){
	$condition .= "`id` LIKE '$id' AND " ;
}
if($name != ""){
	$condition .= "`name` LIKE '$name' AND ";
}
if($start_time_start != "" && $start_time_end != ""){
	$condition .= "`start_time` BETWEEN '$start_time_start' AND '$start_time_end' AND ";
}else if($start_time_start != ""){
	$condition .= "`start_time` >= '$start_time_start' AND ";
}else if($start_time_end != ""){
	$condition .= "`start_time` <= '$start_time_end' AND ";
}
if($end_time_start != "" && $end_time_end != ""){
	$condition .= "`end_time` BETWEEN '$end_time_start' AND '$end_time_end' AND ";
}else if($end_time_start != ""){
	$condition .= "`end_time` >= '$end_time_start' AND ";
}else if($end_time_end != ""){
	$condition .= "`end_time` <= '$end_time_end' AND ";
}
if($step != ""){
	$condition .="`step` LIKE '$step' AND ";
}
if($condition != ""){
	$condition = substr($condition,0,strlen($condition)-4);
	$condition = "WHERE ".$condition;
}
$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM `order` '. $condition .'ORDER BY `start_time` DESC LIMIT '.$index.','.$limit ;
//exit($sql);
$result = mysqli_query($conn, $sql);
if(!$result){
	//$message = array("status" => "error","result" => mysqli_error());
	die(json_encode(mysqli_error($conn)));
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