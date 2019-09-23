<?php
header("Content-Type: text/html;charset=utf-8");

$param_name = array();
//$param_name[0] = 'id';
$param_name[1] = 'name';
$param_name[2] = 'start_time';
$param_name[3] = 'end_time';
$param_name[4] = 'time';
$param_name[5] = 'step';
$param_name[6] = 'trouble_symptom';
$param_name[7] = 'link_id';
$param_name[8] = 'process';
$param_name[9] = 'circuit_number';
$param_name[10] = 'contact_number';
$param_name[11] = 'contact_name';
$param_name[12] = 'area';
$param_name[13] = 'is_trouble';
$param_name[14] = 'is_remote';
$param_name[15] = 'trouble_class';
$param_name[16] = 'trouble_reason';
$param_name[17] = 'business_type';
$param_name[18] = 'remark';
$param_name[19] = 'major';

$param = array();

foreach($param_name as $name){
	if(isset($_POST[$name])){
		if(($name == 'end_time'||$name == 'time'||$name == 'is_trouble'||$name == 'is_remote'||$name == 'link_id')&&$_POST[$name] == ''){
			$param[$name] = null;
		}else{
			$param[$name] = $_POST[$name];
		}
	}else{
		if($name == 'end_time'||$name == 'time'||$name == 'is_trouble'||$name == 'is_remote'||$name == 'link_id'){
			$param[$name] = null;
		}else{
			$param[$name] = '';
		}
	}
}	

$dbhost = 'localhost';  // mysql服务器主机地址
$dbuser = 'gensmo';            // mysql用户名
$dbpass = 'SoSF701TmkYrGY8m';          // mysql用户名密码
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
    die('Could not connect: ' . mysqli_error($conn));
}
mysqli_query($conn , "set names utf8");
mysqli_select_db($conn,'GENSMO');

$sql = 'INSERT INTO `order` (';

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

$result = mysqli_query($conn, $sql);
if(!$result){
	die('error:'.mysqli_error($conn));
}else{
	$name = $param['name'];
	$start_time = $param['start_time'];
	$sql = "select id from `order` WHERE `name` LIKE '$name' AND `start_time` = '$start_time' ORDER BY `create_time` DESC";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_row($result);
	$newID = $row[0];
	$res = array("status" => "success","id" => $newID);
	echo json_encode($res);
}

mysqli_close($conn);
?>
