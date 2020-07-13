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

$do = canDo($token,'getList',$conn);
if($do['status'] != 'success'){
  $res = array("status" => "error","errMsg" => $do['errMsg']);
  exit(json_encode($res));
}

$param_name = ['id','name','start_time_start','start_time_end','end_time_start','end_time_end','number','index','limit','step'];
$param = array();
foreach($param_name as $index => $name){
  if(isset($_POST[$name])){
    if(is_attack($_POST[$name])){
      $res = array("status" => "error","errMsg" => 'Param Illegal');
      exit(json_encode($res));
    } 
    if($name == 'step'){
      $param[$name] = $_POST['step'];
      $param[$name] = explode("|",$param[$name]);
    }else{
      $param[$name] = $_POST[$name];
    }
  }else{
    if($name == 'step'){
      $res = array("status" => "error","errMsg" => 'Param Illegal');
      exit(json_encode($res));
    }else{
      $param[$name] = '';
    }
  }
}

$id = $param['id'];
$name = $param['name'];
$start_time_start = $param['start_time_start'];
$start_time_end = $param['start_time_end'];
$end_time_start = $param['end_time_start'];
$end_time_end = $param['end_time_end'];
$number = $param['number'];
$index = $param['index'];
$limit = $param['limit'];
$step = $param['step'];

if($index == '' && $limit != ''){
  $index = 0;
}
if($index != '' && $limit == ''){
  $index = '';
}

$condition = "";
if($id != ""){
  $condition .= "`id` LIKE '%$id%' AND " ;
}
if($name != ""){
  $condition .= "`name` LIKE '%$name%' AND ";
}
if($start_time_start != "" && $start_time_end != ""){
  $condition .= "`start_time` BETWEEN '$start_time_start' AND '$start_time_end' AND ";
}else if($start_time_start != ""){
  $condition .= "`start_time` >= '$start_time_start' AND ";
}else if($start_time_end != ""){
  $condition .= "`start_time` <= '$start_time_end' AND ";
}
if($end_time_start != "" && $end_time_end != ""){
  $condition .= "`end_time` BETWEEN '$end_time_start' AND '$end_time_end' AND ";
}else if($end_time_start != ""){
  $condition .= "`end_time` >= '$end_time_start' AND ";
}else if($end_time_end != ""){
  $condition .= "`end_time` <= '$end_time_end' AND ";
}
if(count($step) > 1 || $step[0] != ''){
  $step_sql = "(";
  foreach($step as $s){
    $step_sql .="`step` LIKE '$s' OR ";
  }
  $step_sql = substr($step_sql,0,strlen($step_sql)-3);
  $step_sql .= ")";
  $condition .= $step_sql." AND ";
}

if($number != ""){
  $condition .= "`circuit_number` LIKE '%$number%' AND ";
}

if($condition != ""){
  $condition = substr($condition,0,strlen($condition)-4);
  $condition = "WHERE ".$condition;
}
$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM `order` '. $condition .'ORDER BY `create_time` DESC';
if($limit != '' && $index != ''){
  $sql .= ' LIMIT '.$index.','.$limit;
}
//exit($sql);
$result = mysqli_query($conn, $sql);
if(!$result){
  $res = array("status" => "error","errMsg" => 'Mysql Error: '.mysqli_error($conn));
  exit(json_encode($res));
}else{
  $row_number = mysqli_fetch_row(mysqli_query($conn,"SELECT FOUND_ROWS()"))[0];
  $resList = array();
  $i = 0;
  while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
    $resList[$i] = $row;
    $i++;
  }
  $res = array("status" => "success","sum" => $row_number,"result" => $resList);
  echo json_encode($res);
}
mysqli_close($conn);
?>