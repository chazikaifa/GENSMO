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

$do = canDo($token,'getAssessOrder',$conn);
if($do['status'] != 'success'){
  $res = array("status" => "error","errMsg" => $do['errMsg']);
  exit(json_encode($res));
}

if(isset($_POST['START'])&&isset($_POST['END'])){
  $start = $_POST['START'];
  $end = $_POST['END'];
  if(is_attack($start)||is_attack($end)){
    $res = array("status" => "error","errMsg" => 'Param Illegal');
    exit(json_encode($res));
  }
}else{
  $res = array("status" => "error","errMsg" => 'Param Illegal');
  exit(json_encode($res));
}

if(isset($_POST['province'])){
  $province = $_POST['province'];
  if(is_attack($province)){
    $res = array("status" => "error","errMsg" => 'Param Illegal');
    exit(json_encode($res));
  }
}else{
  $province = '';
}
if($province != ''){
  $sql = "SELECT * from  `assess_order` where end_time BETWEEN '$start' AND '$end' AND `province` LIKE '$province'";
}else{
  $sql = "SELECT * from  `assess_order` where end_time BETWEEN '$start' AND '$end'";
}


$result = mysqli_query($conn, $sql);
if(!$result){
  $res = array("status" => 'error','errMsg' => 'mysql error: '.mysqli_error($conn));
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