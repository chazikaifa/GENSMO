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

$do = canDo($token,'process_update',$conn);
if($do['status'] != 'success'){
  $res = array("status" => "error","errMsg" => $do['errMsg']);
  exit(json_encode($res));
}

$param_name = array();
$param_name[0] = 'process_id';
$param_name[1] = 'order_id';
$param_name[2] = 'time';
$param_name[3] = 'description';
$param_name[4] = 'list_order';
$param_name[5] = 'mark';

$param = array();

foreach($param_name as $name){
  if(isset($_POST[$name])){
    if(is_attack($_POST[$name]) || ($name == 'time'||$name == 'list_order'||$name == 'order_id')&&$_POST[$name] == ''){
      $res = array("status" => "error","errMsg" => 'Param Illegal');
      exit(json_encode($res));
    }else{
      $param[$name] = $_POST[$name];
    }
  }else{
    if($name == 'time'||$name == 'list_order'||$name == 'order_id'){
      $res = array("status" => "error","errMsg" => 'Param Illegal');
      exit(json_encode($res));
    }else if($name == 'description'|| $name = 'process_id' || $name = 'mark'){
      $param[$name] = '';
    }
  }
}

$sql = 'INSERT INTO `process` (';

foreach($param_name as $name){
  $sql .= '`'.$name.'`,';
}

$sql = substr($sql,0,strlen($sql)-1);
$sql .= ') VALUES (';

foreach($param as $p){
  $sql .= "'".$p."',";
}
$sql = substr($sql,0,strlen($sql)-1);
$sql .= ')';

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