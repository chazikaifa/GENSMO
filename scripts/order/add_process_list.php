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

$JSON_ERROR = array('JSON_ERROR_NONE','JSON_ERROR_DEPTH','JSON_ERROR_STATE_MISMATCH','JSON_ERROR_CTRL_CHAR','JSON_ERROR_SYNTAX','JSON_ERROR_UTF8');
if(isset($_POST['DATA'])){
  $DATA = $_POST['DATA'];
  $json = json_decode($DATA,true);
  if(!$json){
    $res = array('status' => 'error','errMsg' => 'Decode Error:'.json_last_error_msg());
    exit(json_encode($res));
  }
}else{
  $res = array("status" => "error","errMsg" => 'Param Illegal');
  exit(json_encode($res));
}

$data = $json['data'];

$param_name = array();
$param_name[0] = 'process_id';
$param_name[1] = 'order_id';
$param_name[2] = 'time';
$param_name[3] = 'description';
$param_name[4] = 'list_order';
$param_name[5] = 'mark';

$sql = 'INSERT INTO `process` (';

foreach($param_name as $name){
  $sql .= '`'.$name.'`,';
}

$sql = substr($sql,0,strlen($sql)-1);
$sql .= ') VALUES ';

foreach($data as $d){
  $sql .= '(';
  foreach ($d as $item) {
    $sql .= "'".$item."',";
  }
  $sql = substr($sql,0,strlen($sql)-1);
  $sql .= '),';
}
$sql = substr($sql,0,strlen($sql)-1);

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