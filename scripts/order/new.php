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

$do = canDo($token,'order_new',$conn);
if($do['status'] != 'success'){
  $res = array("status" => "error","errMsg" => $do['errMsg']);
  exit(json_encode($res));
}

$param_name = array();
//$param_name[0] = 'id';
$param_name[1] = 'name';
$param_name[2] = 'start_time';
$param_name[3] = 'end_time';
$param_name[4] = 'time';
$param_name[5] = 'step';
$param_name[6] = 'trouble_symptom';
$param_name[7] = 'link_id';
$param_name[8] = 'process';
$param_name[9] = 'circuit_number';
$param_name[10] = 'contact_number';
$param_name[11] = 'contact_name';
$param_name[12] = 'area';
$param_name[13] = 'is_trouble';
$param_name[14] = 'is_remote';
$param_name[15] = 'trouble_class';
$param_name[16] = 'trouble_reason';
$param_name[17] = 'business_type';
$param_name[18] = 'remark';
$param_name[19] = 'major';
$param_name[20] = 'roomName';
$param_name[21] = 'roomType';
$param_name[22] = 'hiddenDanger';
$param_name[23] = 'reasonDescription';

$param = array();

foreach($param_name as $name){
  if(isset($_POST[$name])){
    if(($name == 'end_time'||$name == 'time'||$name == 'is_trouble'||$name == 'is_remote'||$name == 'link_id'||$name == 'roomName'||$name == 'roomType'||$name == 'hiddenDanger'||$name == 'reasonDescription')&&$_POST[$name] == ''){
      $param[$name] = null;
    }else{
      $param[$name] = $_POST[$name];
    }
  }else{
    if($name == 'end_time'||$name == 'time'||$name == 'is_trouble'||$name == 'is_remote'||$name == 'link_id'||$name == 'roomName'||$name == 'roomType'||$name == 'hiddenDanger'||$name == 'reasonDescription'){
      $param[$name] = null;
    }else{
      $param[$name] = '';
    }
  }
} 

$sql = 'INSERT INTO `order` (';

foreach($param_name as $name){
  $sql .= '`'.$name.'`,';
}

$sql = substr($sql,0,strlen($sql)-1);
$sql .= ') VALUES (';

foreach($param as $p){
  if(!is_null($p)){
    $sql .= "'".$p."',";
  }else{
    $sql .= "NULL,";
  }
  
}
$sql = substr($sql,0,strlen($sql)-1);
$sql .= ')';

$result = mysqli_query($conn, $sql);
if(!$result){
  $res = array("status" => "error","errMsg" => 'Mysql Error: '.mysqli_error($conn));
  exit(json_encode($res));
}else{
  $name = $param['name'];
  $start_time = $param['start_time'];
  $sql = "select `id` from `order` WHERE `name` LIKE '$name' AND `start_time` = '$start_time' ORDER BY `create_time` DESC";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_row($result);
  $newID = $row[0];
  $res = array("status" => "success","id" => $newID);
  echo json_encode($res);
}
mysqli_close($conn);
?>
