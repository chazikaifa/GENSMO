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

$do = canDo($token,'updateAssessOrder',$conn);
if($do['status'] != 'success'){
  $res = array("status" => "error","errMsg" => $do['errMsg']);
  exit(json_encode($res));
}

$param_name = array();
$param_name[0] = 'id';
$param_name[1] = 'title';
$param_name[2] = 'value';

$param = array();

foreach($param_name as $index => $name){
  if(isset($_POST[$name])){
    if($name == 'id' && $_POST[$name] == ''||is_attack($_POST[$name])){
      $res = array("status" => "error","errMsg" => 'Param Illegal');
      exit(json_encode($res));
    }
    $param[$name] = $_POST[$name];
  }else{
    $res = array("status" => "error","errMsg" => 'Param Illegal');
    exit(json_encode($res));
  }
}

$id = $param['id'];
$title = $param['title'];
$value = $param['value'];

$sql = "UPDATE `assess_order` SET `$title`='$value' WHERE `orderId`='$id'";

$result = mysqli_query($conn, $sql);
if(!$result){
  $res = array("status" => 'error','errMsg' => 'Mysql Error: '.mysqli_error($conn));
  exit(json_encode($res));
}else{
  $res = array("status" => "success");
  echo json_encode($res);
}

mysqli_close($conn);
?>