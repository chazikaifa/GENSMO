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

if(isset($_POST['number'])){
  $number = $_POST['number'];
  $number = preg_replace("/\'/","\\\'",$number);
  if($number == '' || preg_match('/%/', $number) || preg_match('/\"/', $number)){
    $res = array("status" => "error","errMsg" => 'Param Illegal');
    exit(json_encode($res));
  }
}else{
  $res = array("status" => "error","errMsg" => 'Param Illegal');
  exit(json_encode($res));
}

$conn = LinkDB();
if(! $conn ){
  $res = array("status" => "error","errMsg" => 'Mysql Error: '.mysqli_error($conn));
  exit(json_encode($res));
}

$do = canDo($token,'getCircuitByNumber',$conn);
if($do['status'] != 'success'){
  $res = array("status" => "error","errMsg" => $do['errMsg']);
  exit(json_encode($res));
}

$sql = "SELECT  * FROM `number_exchange` WHERE `circuit_number` LIKE '%$number%'";
$result = mysqli_query($conn, $sql);
if(!$result){
  $res = array("status" => "error","errMsg" => 'Mysql Error: '.mysqli_error($conn));
  exit(json_encode($res));
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