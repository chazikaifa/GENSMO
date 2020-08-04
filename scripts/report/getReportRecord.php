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

$do = canDo($token,'setReportRecord',$conn);
if($do['status'] != 'success'){
  $res = array("status" => "error","errMsg" => $do['errMsg']);
  exit(json_encode($res));
}

if(isset($_POST['type'])&&!is_attack($_POST['type'])){
  $type = $_POST['type'];
}else{
  $res = array("status" => "error","errMsg" => 'Param Illegal');
  exit(json_encode($res));
}

if(isset($_POST['start'])&&!is_attack($_POST['start'])){
  $start = $_POST['start'];
}else{
  $res = array("status" => "error","errMsg" => 'Param Illegal');
  exit(json_encode($res));
}

if(isset($_POST['end'])&&!is_attack($_POST['end'])){
  $end = $_POST['end'];
}else{
  $res = array("status" => "error","errMsg" => 'Param Illegal');
  exit(json_encode($res));
}

if(isset($_POST['data'])){
  $data = $_POST['data'];
}else{
  $res = array("status" => "error","errMsg" => 'Param Illegal');
  exit(json_encode($res));
}

$sql = "INSERT INTO `report_record` (`start`,`end`,`type`,`data`) VALUES ('$start','$end','$type','$data')";
$result = mysqli_query($conn, $sql);

if(!$result){
  $res = array("status" => "error","errMsg" => 'Mysql Error: '.mysqli_error($conn));
  exit(json_encode($res));
}else{
  $sql = "SELECT MAX(id) FROM `report_record`";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_row($result);
  $newID = $row[0];
  $res = array("status" => "success","id" => $newID);
  echo json_encode($res);
}
mysqli_close($conn);
?>