<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

if(isset($_POST['mark'])){
	$mark = $_POST['mark'];
}else{
	$res = array("status" => 'error','errMsg' => 'param not set');
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

if($mark != ''){
	$sql = "SELECT `assess_customer`.`name`,mark,count(`number_exchange`.`circuit_number`) as sum FROM `assess_customer` LEFT JOIN `number_exchange` ON `assess_customer`.`name` = `number_exchange`.`name` where mark LIKE '$mark' GROUP by `assess_customer`.`name` HAVING sum>0 order by sum desc";
}else{
	$sql = "SELECT `assess_customer`.`name`,mark,count(`number_exchange`.`circuit_number`) as sum FROM `assess_customer` LEFT JOIN `number_exchange` ON `assess_customer`.`name` = `number_exchange`.`name` GROUP by `assess_customer`.`name` HAVING sum>0 order by sum desc";
}


$result = mysqli_query($conn, $sql);
if(!$result){
	$res = array("status" => 'error','errMsg' => 'mysql error: '.mysqli_error($conn));
	exit(json_encode($res));
}else{
	$resList = array();
	$i = 0;
	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
		$resList[$i] = $row;
		$i++;
	}
	$res = array("status" => "success","result" => $resList,"mark" => $mark);
	echo json_encode($res);
}

mysqli_close($conn);
?>