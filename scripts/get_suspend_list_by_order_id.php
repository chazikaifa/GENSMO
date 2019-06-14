<?php
header("Content-Type: text/html;charset=utf-8");
if(isset($_POST['order_id'])){
	$id = $_POST['order_id'];
}else{
	die('id is NOT set!');
}


$dbhost = 'localhost';  // mysql服务器主机地址
$dbuser = 'root';            // mysql用户名
$dbpass = '';          // mysql用户名密码
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(! $conn ){
    die('Could not connect: ' . mysqli_error($conn));
}
mysqli_query($conn , "set names utf8");
mysqli_select_db($conn,'GENSMO');

$sql = 'SELECT  * FROM `suspend` WHERE `order_id` LIKE \''.$id.'\' ORDER BY `start_time` ASC' ;
//exit($sql);
$result = mysqli_query($conn, $sql);
if(!$result){
	//$message = array("status" => "error","result" => mysqli_error());
	die(mysqli_error($conn));
}else{
	$resList = array();
	$i = 0;
	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
		$resList[$i] = $row;
		$i++;
	}
	$res = array("status" => "success","result" => $resList);
	echo json_encode($res);
}
mysqli_close($conn);
?>