<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

include('../system/DB.php');

if(isset($_POST['number']) && isset($_POST['password'])){
  $number = $_POST['number'];
  $password = base64_decode($_POST['password']);
}else{
  exit(json_encode(array("status" => "error","errMsg" => 'Param Illegal')));
}

if(is_attack($number)||is_attack($_POST['password'])){
  exit(json_encode(array("status" => "error","errMsg" => 'Param Illegal')));
}

$conn = LinkDB();
if(! $conn ){
  exit(json_encode(array("status" => "error","result" => "mysql connect error: ".mysqli_error($conn))));
}
$sql = "SELECT name FROM `user` WHERE `number` LIKE '$number' AND `password` LIKE '$password'";

$result = mysqli_query($conn, $sql);
if($result){
  $row = mysqli_fetch_array($result);
  if($row){
    $seed = $number.rand(0,10000)*time();
    $token = base64_encode($seed);
    $time = date("Y-m-d H:i:s", strtotime("+12 hour"));
    $sql = "UPDATE `user` SET `token`='$token',`token_time`='$time' WHERE `number` LIKE '$number' AND `password` LIKE '$password'";
    $re = mysqli_query($conn, $sql);
    $res = array("status" => "success","token" => $token,"name" => $row['name']);
    echo json_encode($res);
  }else{
    $res = array("status" => "fail");
    echo json_encode($res);
  }
}else{
  $res = array("status" => "error","errMsg" => 'SQL fail:'.mysqli_error($conn));
  echo json_encode($res);
}
?>