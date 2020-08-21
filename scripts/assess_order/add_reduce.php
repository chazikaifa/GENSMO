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
$param_name[1] = 'province';
$param_name[2] = 'time';
$param_name[3] = 'reduce_time';
$param_name[4] = 'time_limit';
$param_name[5] = 'TOP33';
$param_name[6] = 'TOP210';
$param_name[7] = 'TOP800';
$param_name[8] = 'TOPN';
$param_name[9] = 'assess_TOPN';

$param = array();

foreach($param_name as $name){
  if(isset($_POST[$name])){
    if(is_attack($_POST[$name]) || ($name == 'id')&&$_POST[$name] == ''){
      $res = array("status" => "error","errMsg" => 'Param Illegal');
      exit(json_encode($res));
    }else{
      $param[$name] = $_POST[$name];
    }
  }else{
    $res = array("status" => "error","errMsg" => 'Param Illegal');
    exit(json_encode($res));
  }
}

$id = $param['id'];
$province = $param['province'];
$time = $param['time'];
$reduce_time = $param['reduce_time'];
$time_limit = (int)$param['time_limit'];
$assess_time = (int)($time) - (int)($reduce_time);
$TOP33 = $param['TOP33'];
$TOP210 = $param['TOP210'];
$TOP800 = $param['TOP800'];
$TOPN = $param['TOPN'];
$assess_TOPN = $param['assess_TOPN'];

if($assess_time > $time_limit){
  $timeout = 1;
}else{
  $timeout = 0;
}

if($province != ''){
  if($province == '用户'){
    $sql = "UPDATE `assess_order` SET `responsible_province`='$province',`reduce_time`=$reduce_time,`assessment_time`=$assess_time,`time_out`=$timeout,`is_assess`='0',`time_limit`=$time_limit,`TOP33`=$TOP33,`TOP210`=$TOP210,`TOP800`=$TOP800,`TOPN`=$TOPN,`assess_TOPN`=$assess_TOPN WHERE `orderID`='$id'";
  }else{
    $sql = "UPDATE `assess_order` SET `responsible_province`='$province',`reduce_time`=$reduce_time,`assessment_time`=$assess_time,`time_out`=$timeout,`is_assess`='1',`time_limit`=$time_limit,`TOP33`=$TOP33,`TOP210`=$TOP210,`TOP800`=$TOP800,`TOPN`=$TOPN,`assess_TOPN`=$assess_TOPN WHERE `orderID`='$id'";
  }
}else{
  $sql = "UPDATE `assess_order` SET `reduce_time`=$reduce_time,`assessment_time`=$assess_time,`time_out`=$timeout,`time_limit`=$time_limit,`TOP33`=$TOP33,`TOP210`=$TOP210,`TOP800`=$TOP800,`TOPN`=$TOPN,`assess_TOPN`=$assess_TOPN WHERE `orderID`='$id'";
}

// $res = array("status" => "error","errMsg" => $sql);
// exit(json_encode($res));

$result = mysqli_query($conn, $sql);
if(!$result){
  $res = array("status" => 'error','errMsg' => 'Mysql Error: '.mysqli_error($conn));
  exit(json_encode($res));
}else{
  $row = mysqli_affected_rows($conn);
  $res = array("status" => "success","row" => $row);
  echo json_encode($res);
}
mysqli_close($conn);
?>