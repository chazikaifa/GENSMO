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
  $condition .= "`order`.`id` LIKE '%$id%' AND " ;
}
if($name != ""){
  $condition .= "`order`.`name` LIKE '%$name%' AND ";
}
if($start_time_start != "" && $start_time_end != ""){
  $condition .= "`order`.`start_time` BETWEEN '$start_time_start' AND '$start_time_end' AND ";
}else if($start_time_start != ""){
  $condition .= "`order`.`start_time` >= '$start_time_start' AND ";
}else if($start_time_end != ""){
  $condition .= "`order`.`start_time` <= '$start_time_end' AND ";
}
if($end_time_start != "" && $end_time_end != ""){
  $condition .= "`order`.`end_time` BETWEEN '$end_time_start' AND '$end_time_end' AND ";
}else if($end_time_start != ""){
  $condition .= "`order`.`end_time` >= '$end_time_start' AND ";
}else if($end_time_end != ""){
  $condition .= "`order`.`end_time` <= '$end_time_end' AND ";
}
if(count($step) > 1 || $step[0] != ''){
  $step_sql = "(";
  foreach($step as $s){
    $step_sql .="`order`.`step` LIKE '$s' OR ";
  }
  $step_sql = substr($step_sql,0,strlen($step_sql)-3);
  $step_sql .= ")";
  $condition .= $step_sql." AND ";
}

if($number != ""){
  $condition .= "`order`.`circuit_number` LIKE '%$number%' AND ";
}

if($condition != ""){
  $condition = substr($condition,0,strlen($condition)-4);
  $condition = "WHERE ".$condition;
}
$sql = 'SELECT SQL_CALC_FOUND_ROWS `order`.*,`assess_customer`.`mark` FROM `order` LEFT JOIN `assess_customer` on `order`.`name`=`assess_customer`.`name` '. $condition .'ORDER BY `create_time` DESC';
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
    if($row['step'] != '结单' && $row['step'] != '已撤销'){
      $order_id = $row['id'];

      $sql = "SELECT `time`,`mark`,`list_order` FROM `process` WHERE `order_id` LIKE '$order_id' AND mark IN ('set_suspend','unset_suspend') ORDER BY `list_order` ASC";
      $process_res = mysqli_query($conn, $sql);
      $pro = mysqli_fetch_all($process_res);
      $flag = false;
      $start = NULL;
      $reduce_time = 0;
      foreach ($pro as $p) {
        if($flag && $p[1] == 'unset_suspend'){
          $d = (strtotime($p[0]) - $start);
          $reduce_time += $d;
          $flag = false;
          $reduce_time = NULL;
        }else if($p[1] == 'set_suspend'){
          $flag = true;
          $start = strtotime($p[0]);
        }
      }
      if($flag){
        $d = time()-$start;
        $reduce_time += $d;
      }
      $time = time() - strtotime($row['start_time']) - $reduce_time;
      $time = round($time/60,2);
      $row['net_duration'] = $time;
    }

    $resList[$i] = $row;
    $i++;
  }
  $res = array("status" => "success","sum" => $row_number,"result" => $resList);
  echo json_encode($res);
}
mysqli_close($conn);
?>