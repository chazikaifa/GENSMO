<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

$JSON_ERROR = array('JSON_ERROR_NONE','JSON_ERROR_DEPTH','JSON_ERROR_STATE_MISMATCH','JSON_ERROR_CTRL_CHAR','JSON_ERROR_SYNTAX','JSON_ERROR_UTF8');
if(isset($_POST['DATA'])){
  $DATA = $_POST['DATA'];
  $json = json_decode($DATA,true);
  if(!$json){
    $res = array('status' => 'error','errMsg' => json_last_error_msg());
    exit(json_encode($res));
  }
}else{
  $res = array("status" => 'error','errMsg' => 'data not set');
  exit(json_encode($res));
}

$arr = $json['data'];
// $title = $json['title'];
// $data = [];
// foreach ($title as $t) {
//  $data[$t] = [];
// }
// foreach ($arr as $json_obj) {
//  foreach($json_obj as $key => $value){
//    $data[$key] []= $value;
//  }
// }

$dbhost = 'localhost';  // mysql服务器主机地址
$dbuser = 'gensmo';            // mysql用户名
$dbpass = 'SoSF701TmkYrGY8m';          // mysql用户名密码
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
  $res = array("status" => 'error','errMsg' => 'mysql error: '.mysqli_error($conn));
  exit(json_encode($res));
}
mysqli_query($conn , "set names utf8");
mysqli_select_db($conn,'GENSMO');

// $sql = 'UPDATE `assess_order` SET `TOP210`= 1,`net_duration` = CASE `orderId` ';
// $ids = '';
// foreach ($arr as $obj) {
//  $key = $obj['orderId'];
//  $value = $obj['net_duration'];
//  $sql .= "WHEN '$key' THEN $value ";
//  $ids .= "'$key',";
// }
// $ids = substr($ids,0,strlen($ids)-1);
// $sql .= "END WHERE `orderId` IN ($ids)";
$ids = '';
foreach ($arr as $obj) {
  $key = $obj['orderId'];
  $ids .= "'$key',";
}
$ids = substr($ids,0,strlen($ids)-1);
$sql = "UPDATE `assess_order` SET `TOP210`= 1 WHERE `orderId` IN ($ids)";

$result = mysqli_query($conn, $sql);
if(!$result){
  $res = array("status" => 'error','errMsg' => 'mysql error: '.mysqli_error($conn));
  exit(json_encode($res));
}else{
  $res = array("status" => "success");
  echo json_encode($res);
}

mysqli_close($conn);
?>