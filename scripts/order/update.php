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

$do = canDo($token,'order_update',$conn);
if($do['status'] != 'success'){
  $res = array("status" => "error","errMsg" => $do['errMsg']);
  exit(json_encode($res));
}

$param_name = array();
$param_name[0] = 'id';
$param_name[1] = 'name';
$param_name[2] = 'start_time';
$param_name[3] = 'end_time';
$param_name[4] = 'time';
$param_name[5] = 'time_limit';
$param_name[6] = 'net_duration';
$param_name[7] = 'step';
$param_name[8] = 'trouble_symptom';
$param_name[9] = 'link_id';
$param_name[10] = 'process';
$param_name[11] = 'circuit_number';
$param_name[12] = 'contact_number';
$param_name[13] = 'contact_name';
$param_name[14] = 'area';
$param_name[15] = 'is_trouble';
$param_name[16] = 'is_remote';
$param_name[17] = 'trouble_class';
$param_name[18] = 'trouble_reason';
$param_name[19] = 'business_type';
$param_name[20] = 'remark';
$param_name[21] = 'major';
$param_name[22] = 'roomName';
$param_name[23] = 'roomType';
$param_name[24] = 'hiddenDanger';
$param_name[25] = 'reasonDescription';

$param = array();

foreach($param_name as $index => $name){
  if(isset($_POST[$name])){
    if(($name == 'end_time'||$name == 'time'||$name == 'net_duration'||$name == 'is_trouble'||$name == 'is_remote'||$name == 'link_id'||$name == 'roomName'||$name == 'roomType'||$name == 'hiddenDanger'||$name == 'reasonDescription')&&$_POST[$name] == ''){
      $param[$name] = null;
    }else{
      $param[$name] = $_POST[$name];
    }
  }else if($name == 'id'){
    $res = array("status" => "error","errMsg" => 'Param Illegal');
    exit(json_encode($res));
  }else{
    unset($param_name[$index]); 
  }
}
array_values($param_name);

$sql = "UPDATE `order` SET ";

foreach($param_name as $name){
  if($name != 'id'){
    $sql .= '`'.$name.'` = ';
    if(!is_null($param[$name])){
      $sql .= "'".$param[$name]."', ";
    }else{
      $sql .= "NULL, ";
    }
  }
}
$id = $param['id'];
$sql = substr($sql,0,strlen($sql)-2);
$sql .= " WHERE `id` = '$id'";

$result = mysqli_query($conn, $sql);
if(!$result){
  $res = array("status" => "error","errMsg" => 'Mysql Error: '.mysqli_error($conn));
  exit(json_encode($res));
}else{
  $res = array("status" => "success");
  echo json_encode($res);
}

mysqli_close($conn);