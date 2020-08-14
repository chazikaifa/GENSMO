<?php
require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

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

$do = canDo($token,'export',$conn);
if($do['status'] != 'success'){
  $res = array("status" => "error","errMsg" => $do['errMsg']);
  exit(json_encode($res));
}

$param_name = ['id','name','start_time_start','start_time_end','end_time_start','end_time_end','number','step'];
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
$step = $param['step'];

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
if($condition != ""){
  $condition = substr($condition,0,strlen($condition)-4);
  $condition = "WHERE ".$condition;
}
$sql = 'SELECT SQL_CALC_FOUND_ROWS `order`.*,`assess_customer`.`mark` FROM `order` LEFT JOIN `assess_customer` on `order`.`name`=`assess_customer`.`name` '. $condition .'ORDER BY `create_time` DESC';
//exit($sql);
$result = mysqli_query($conn, $sql);
if(!$result){
  $res = array("status" => "error","errMsg" => 'Mysql Error: '.mysqli_error($conn));
  exit(json_encode($res));
}else{
  $row_number = mysqli_fetch_row(mysqli_query($conn,"SELECT FOUND_ROWS()"))[0];
  
  if($row_number == 0){
    $res = array("status" => "error","errMsg" => "Empty Result");
    exit(json_encode($res));
  }
  $sheet->setCellValueByColumnAndRow(1,1, '故障单编号');
  $sheet->setCellValueByColumnAndRow(2,1, '客户名称');
  $sheet->setCellValueByColumnAndRow(3,1, '故障受理时间');
  $sheet->setCellValueByColumnAndRow(4,1, '故障修复时间');
  $sheet->setCellValueByColumnAndRow(5,1, '故障历时(分钟)');
  $sheet->setCellValueByColumnAndRow(6,1, '故障净历时(分钟)');
  $sheet->setCellValueByColumnAndRow(7,1, '故障恢复时限');  
  $sheet->setCellValueByColumnAndRow(8,1, '工单状态');
  $sheet->setCellValueByColumnAndRow(9,1, '故障简述');
  $sheet->setCellValueByColumnAndRow(10,1, '19工单编号');
  $sheet->setCellValueByColumnAndRow(11,1, '故障进展');
  $sheet->setCellValueByColumnAndRow(12,1, '电路编号');
  $sheet->setCellValueByColumnAndRow(13,1, '客户联系方式');
  $sheet->setCellValueByColumnAndRow(14,1, '客户联系人');
  $sheet->setCellValueByColumnAndRow(15,1, '区域');
  $sheet->setCellValueByColumnAndRow(16,1, '是否故障');
  $sheet->setCellValueByColumnAndRow(17,1, '是否客户原因');
  $sheet->setCellValueByColumnAndRow(18,1, '故障分类');
  $sheet->setCellValueByColumnAndRow(19,1, '原因细化');
  $sheet->setCellValueByColumnAndRow(20,1, '行业类型');
  $sheet->setCellValueByColumnAndRow(21,1, '责任专业');
  $sheet->setCellValueByColumnAndRow(22,1, '备注');
  $sheet->setCellValueByColumnAndRow(23,1, '客户标记');
  $sheet->setCellValueByColumnAndRow(24,1, '统一标识');
  
  $i = 2;
  
  while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
    $sheet->setCellValueByColumnAndRow(1,$i, $row['id']);
    $sheet->setCellValueByColumnAndRow(2,$i, $row['name']);
    $sheet->setCellValueByColumnAndRow(3,$i, $row['start_time']);
    $sheet->setCellValueByColumnAndRow(4,$i, $row['end_time']);
    $sheet->setCellValueByColumnAndRow(5,$i, $row['time']);
    $sheet->setCellValueByColumnAndRow(6,$i, $row['net_duration']);
    $sheet->setCellValueByColumnAndRow(7,$i, $row['time_limit']);

    $sheet->setCellValueByColumnAndRow(8,$i, $row['step']);
    $sheet->setCellValueByColumnAndRow(9,$i, $row['trouble_symptom']);
    $sheet->setCellValueByColumnAndRow(10,$i, $row['link_id']);
    
    $process = '';
    
    $conn_p = LinkDB();
    $row_id = $row['id'];
    $sql_p = "SELECT * FROM `process` WHERE `order_id` LIKE '$row_id' ORDER BY `list_order` ASC";
    $result_p = mysqli_query($conn_p, $sql_p);
    $index_p = 1;
    while($row_p = mysqli_fetch_array($result_p,MYSQLI_ASSOC)){
      switch($row_p['mark']){
        case "set_suspend":
          $mark = "[挂起]";
          break;
        case "unset_suspend":
          $mark = "[解挂]";
          break;
        case "":
          $mark = "[进展]";
          break;
        default:
          $mark = "[进展]";
          break;
      }
      $process .= $index_p."、".$row_p['time']." ".$mark." ".$row_p['description']."\n"; 
      $index_p += 1;
    }
    mysqli_close($conn_p);

    $sheet->setCellValueByColumnAndRow(11,$i, $process);
    
    $sheet->setCellValueByColumnAndRow(12,$i, $row['circuit_number']);
    $sheet->setCellValueByColumnAndRow(13,$i, $row['contact_number']);
    $sheet->setCellValueByColumnAndRow(14,$i, $row['contact_name']);
    $sheet->setCellValueByColumnAndRow(15,$i, $row['area']);
    
    $unify_mark = '{';

    if($row['is_trouble'] == 0){
      $is_trouble = '否';
      $unify_mark .= "否|";
    }else{
      $is_trouble = '是';
      $unify_mark .= $row['trouble_class'].'-'.$row['trouble_reason'].'|';
    }
    $sheet->setCellValueByColumnAndRow(16,$i, $is_trouble);
    
    if($row['is_customer'] == 0){
      $is_customer = '否';
    }else{
      $is_customer = '是';
    }
    $sheet->setCellValueByColumnAndRow(17,$i, $is_customer);
    $sheet->setCellValueByColumnAndRow(18,$i, $row['trouble_class']);
    $sheet->setCellValueByColumnAndRow(19,$i, $row['trouble_reason']);
    $sheet->setCellValueByColumnAndRow(20,$i, $row['business_type']);
    $sheet->setCellValueByColumnAndRow(21,$i, $row['major']);
    $sheet->setCellValueByColumnAndRow(22,$i, $row['remark']);
    $sheet->setCellValueByColumnAndRow(23,$i, $row['mark']);

    $unify_mark .= $row['area'].'|';
    $unify_mark .= $row['roomName'].'|';
    $unify_mark .= $row['roomType'].'|';
    $unify_mark .= $row['reasonDescription'].'|';
    $unify_mark .= $row['process'].'|';
    $unify_mark .= $row['hiddenDanger'].'}';
    $sheet->setCellValueByColumnAndRow(24,$i, $unify_mark);

    $i++;
  }
  
  $writer = new Xlsx($spreadsheet);
  date_default_timezone_set('PRC');
  $d = date('Ymd',time());
  $saveName = $d.'-'.uniqid().'.xlsx';
  $writer->save('../../files/'.$saveName);
  
  $res = array("status" => "success","sum" => $row_number,"fileName" => $saveName);
  echo json_encode($res);
}
mysqli_close($conn);
?>