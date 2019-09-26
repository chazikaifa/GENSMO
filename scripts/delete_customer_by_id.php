<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

if(isset($_POST['customer_id'])){
	$id = $_POST['customer_id'];
}else{
	die("id is NOT set!");
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

$sql = "DELETE FROM `customer` WHERE `id` = '$id'";
$result = mysqli_query($conn, $sql);
if(!$result){
	die('error:'.mysqli_error($conn));
}else{
	$res = array("status" => "success");
	echo json_encode($res);
}

mysqli_close($conn);