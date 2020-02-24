<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
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
	$step = explode("|",$step);
}else{
	$step = [""];
}
$dbhost = 'localhost';  // mysql服务器主机地址
$dbuser = 'gensmo';            // mysql用户名
$dbpass = 'SoSF701TmkYrGY8m';          // mysql用户名密码
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
	$condition .= "`id` LIKE '%$id%' AND " ;
}
if($name != ""){
	$condition .= "`name` LIKE '%$name%' AND ";
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
if(count($step) > 1 || $step[0] != ''){
	$step_sql = "(";
	foreach($step as $s){
		$step_sql .="`step` LIKE '$s' OR ";
	}
	$step_sql = substr($step_sql,0,strlen($step_sql)-3);
	$step_sql .= ")";
	$condition .= $step_sql." AND ";
}

if($number != ""){
	$condition .= "`circuit_number` LIKE '%$number%' AND ";
}

if($condition != ""){
	$condition = substr($condition,0,strlen($condition)-4);
	$condition = "WHERE ".$condition;
}
$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM `order` '. $condition .'ORDER BY `create_time` DESC';
if($limit != '' && $index != ''){
	$sql .= ' LIMIT '.$index.','.$limit;
}
//exit($sql);
$result = mysqli_query($conn, $sql);
if(!$result){
	//$message = array("status" => "error","result" => mysqli_error());
	die(mysqli_error($conn));
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