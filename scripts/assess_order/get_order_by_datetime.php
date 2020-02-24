<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

if(isset($_POST['START'])&&isset($_POST['END'])){
	$start = $_POST['START'];
	$end = $_POST['END'];
}else{
	$res = array("status" => 'error','errMsg' => 'date not set');
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

$sql = 'SELECT * from  `assess_order` where end_time BETWEEN \''.$start.'\' AND \''.$end.'\'';

$result = mysqli_query($conn, $sql);
if(!$result){
	$res = array("status" => 'error','errMsg' => 'mysql error: '.mysqli_error($conn));
	exit(json_encode($res));
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