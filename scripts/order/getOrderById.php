<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

include('../system/canDo.php');
include('../system/DB.php');

if(isset($_POST['token'])){
  $token = $_POST['token'];
  if(is_attack($token)){
    $res = array("status" => "error","errMsg" => 'Token Unavailable');
    exit(json_encode($res));
  }
}else{
  $res = array("status" => "error","errMsg" => 'Token Unavailable');
  exit(json_encode($res));
}

$conn = LinkDB();
if(! $conn ){
  $res = array("status" => "error","errMsg" => 'Mysql Error: '.mysqli_error($conn));
  exit(json_encode($res));
}

$do = canDo($token,'order_view',$conn);
if($do['status'] != 'success'){
  $res = array("status" => "error","errMsg" => $do['errMsg']);
  exit(json_encode($res));
}

if(isset($_POST['id'])&&!is_attack($_POST['id'])){
  $id = $_POST['id'];
}else{
  $res = array("status" => "error","errMsg" => 'Param Illegal');
  exit(json_encode($res));
}

$sql = "SELECT `order`.*,`assess_customer`.`mark` FROM `order` LEFT JOIN `assess_customer` on `order`.`name`=`assess_customer`.`name` WHERE `id` LIKE '$id'";
$result = mysqli_query($conn, $sql);

if(!$result){
  $res = array("status" => "error","errMsg" => 'Mysql Error: '.mysqli_error($conn));
  exit(json_encode($res));
}else{
  if($row = mysqli_fetch_array($result)){
    $row["status"] = "success";
    echo json_encode($row);
  }else{
    echo json_encode(array("status" => "error","errMsg" => "Empty Result"));
  }
}
mysqli_close($conn);