<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

$JSON_ERROR = array('JSON_ERROR_NONE','JSON_ERROR_DEPTH','JSON_ERROR_STATE_MISMATCH','JSON_ERROR_CTRL_CHAR','JSON_ERROR_SYNTAX','JSON_ERROR_UTF8');
if(isset($_POST['DATA'])){
	$DATA = $_POST['DATA'];
	$json = json_decode($DATA,true);
	if(!$json){
		$res = array('status' => 'error','errMsg' => json_last_error_msg());
		exit(json_encode($res));
	}
}else{
	$res = array("status" => 'error','errMsg' => 'data not set');
	exit(json_encode($res));
}

$param_name = array();
$param = array();
foreach($json as $key => $value){
	$param_name[] = $key;
	$param[$key] = $value;
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

$sql = 'INSERT INTO `number_exchange` (';

foreach($param_name as $name){
	$sql .= '`'.$name.'`,';
}

$sql = substr($sql,0,strlen($sql)-1);
$sql .= ') VALUES (';

foreach($param as $p){
	if(!is_null($p)){
		$sql .= "'".$p."',";
	}else{
		$sql .= "NULL,";
	}
	
}
$sql = substr($sql,0,strlen($sql)-1);
$sql .= ')';

$sql .= ' ON DUPLICATE KEY UPDATE ';
foreach ($param_name as $key) {
	if($key != 'circuit_number'){
		$sql .= "$key = VALUES($key),";
	}
}
$sql = substr($sql,0,strlen($sql)-1);
$sql .= ";";

$result = mysqli_query($conn, $sql);
if(!$result){
	$res = array("status" => 'error','errMsg' => 'mysql error: '.mysqli_error($conn));
	exit(json_encode($res));
}else{
	$res = array("status" => "success");
	echo json_encode($res);
}

mysqli_close($conn);
?>