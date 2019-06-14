<?php
header("Content-Type: text/html;charset=utf-8");

if(isset($_POST['suspend_id'])){
	$id = $_POST['suspend_id'];
}else{
	die("id is NOT set!");
}

$dbhost = 'localhost';  // mysql服务器主机地址
$dbuser = 'root';            // mysql用户名
$dbpass = '';          // mysql用户名密码
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
    die('Could not connect: ' . mysqli_error($conn));
}
mysqli_query($conn , "set names utf8");
mysqli_select_db($conn,'GENSMO');

$sql = "DELETE FROM `suspend` WHERE `suspend_id` = '$id'";
$result = mysqli_query($conn, $sql);
if(!$result){
	die('error:'.mysqli_error($conn));
}else{
	echo "success";
}

mysqli_close($conn);