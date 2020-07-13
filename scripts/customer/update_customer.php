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

if(isset($_POST['id'])&&$_POST['id'] != ''){
  $id = $_POST['id'];
  if(is_attack($name)){
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

$do = canDo($token,'updateCustomer',$conn);
if($do['status'] != 'success'){
  $res = array("status" => "error","errMsg" => $do['errMsg']);
  exit(json_encode($res));
}

$param_name = array();
$param_name[0] = 'id';
$param_name[1] = 'unify_name';
$param_name[2] = 'name';
$param_name[3] = 'mark';
$param_name[4] = 'level';
$param_name[5] = 'N_manager';
$param_name[6] = 'NM_phone';
$param_name[7] = 'C_manager';
$param_name[8] = 'CM_phone';
// $param_name[9] = 'update_date';
$param_name[10] = 'origin';
$param_name[11] = 'remark';

$param = array();

foreach($param_name as $name){
  if(isset($_POST[$name])){
    $param[$name] = $_POST[$name];
  }else{
    $param[$name] = NULL;
  }
}

$sql = "UPDATE `customer` SET ";

foreach($param_name as $name){
  if($name != 'id'){
    if(!($param[$name] === NULL)){
      $sql .= '`'.$name.'` = ';
      $sql .= "'".$param[$name]."', ";
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
?>
