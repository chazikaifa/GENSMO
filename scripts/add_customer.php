<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type'); 

$param_name = array();
//$param_name[0] = 'id';
$param_name[1] = 'unify_name';
$param_name[2] = 'name';
$param_name[3] = 'mark';
$param_name[4] = 'level';
$param_name[5] = 'N_manager';
$param_name[6] = 'NM_phone';
$param_name[7] = 'C_manager';
$param_name[8] = 'CM_phone';
// $param_name[9] = 'update_date';
$param_name[10] = 'origin';
$param_name[11] = 'remark';

$param = array();

foreach($param_name as $name){
	if(isset($_POST[$name])){
		$param[$name] = $_POST[$name];
	}else{
		$param[$name] = '';
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

$sql = 'INSERT INTO `customer` (';

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
	$res = array("status" => "success");
	echo json_encode($res);
}

mysqli_close($conn);
?>
