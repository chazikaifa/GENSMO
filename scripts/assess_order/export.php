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

$sql = "SELECT * from  `assess_order` where end_time BETWEEN '$start' AND '$end' AND `responsible_province` LIKE '广州' AND `correct_province` IN ('广州','用户')";

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
  $sheet->setCellValueByColumnAndRow(3,1, '行业类型');
  $sheet->setCellValueByColumnAndRow(4,1, '客户等级');
  $sheet->setCellValueByColumnAndRow(5,1, '故障业务类型（一级）');
  $sheet->setCellValueByColumnAndRow(6,1, '（二级）');
  $sheet->setCellValueByColumnAndRow(7,1, '电路编号');
  $sheet->setCellValueByColumnAndRow(8,1, '故障简述');
  $sheet->setCellValueByColumnAndRow(9,1, '故障开始时间');
  $sheet->setCellValueByColumnAndRow(10,1, '故障销账时间');
  $sheet->setCellValueByColumnAndRow(11,1, '总历时');
  $sheet->setCellValueByColumnAndRow(12,1, '净历时');
  $sheet->setCellValueByColumnAndRow(13,1, '考核历时');
  $sheet->setCellValueByColumnAndRow(14,1, '减免历时');
  $sheet->setCellValueByColumnAndRow(15,1, '时限');
  $sheet->setCellValueByColumnAndRow(16,1, '是否超时');
  $sheet->setCellValueByColumnAndRow(17,1, '故障原因');
  $sheet->setCellValueByColumnAndRow(18,1, '故障原因简述');
  $sheet->setCellValueByColumnAndRow(19,1, '是否考核');
  $sheet->setCellValueByColumnAndRow(20,1, '故障段落');
  $sheet->setCellValueByColumnAndRow(21,1, '地市');
  $sheet->setCellValueByColumnAndRow(22,1, '故障责任地市');
  $sheet->setCellValueByColumnAndRow(23,1, 'TOP33');
  $sheet->setCellValueByColumnAndRow(24,1, 'TOP160');
  $sheet->setCellValueByColumnAndRow(25,1, 'TOP210');
  $sheet->setCellValueByColumnAndRow(26,1, 'TOP800');
  $sheet->setCellValueByColumnAndRow(27,1, 'TOPN');
  $sheet->setCellValueByColumnAndRow(28,1, '考核TOPN');
  $sheet->setCellValueByColumnAndRow(29,1, '故障处理单元');
  $sheet->setCellValueByColumnAndRow(30,1, '责任专业');
  $sheet->setCellValueByColumnAndRow(31,1, '是否真实故障');
  $sheet->setCellValueByColumnAndRow(32,1, '故障大类');
  $sheet->setCellValueByColumnAndRow(33,1, '故障原因细分');
  $sheet->setCellValueByColumnAndRow(34,1, '区域');
  $sheet->setCellValueByColumnAndRow(35,1, '机房名称');
  $sheet->setCellValueByColumnAndRow(36,1, '机房类型');
  $sheet->setCellValueByColumnAndRow(37,1, '故障详情');
  $sheet->setCellValueByColumnAndRow(38,1, '隐患');
  $sheet->setCellValueByColumnAndRow(39,1, '备注');
  $sheet->setCellValueByColumnAndRow(40,1, '责任地市澄清');
  
  $i = 2;
  
  while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
    $sheet->setCellValueByColumnAndRow(1,$i, $row['orderId']);
    $sheet->setCellValueByColumnAndRow(2,$i, $row['name']);
    $sheet->setCellValueByColumnAndRow(3,$i, $row['business_type']);
    $sheet->setCellValueByColumnAndRow(4,$i, $row['level']);
    $sheet->setCellValueByColumnAndRow(5,$i, $row['trouble_type']);
    $sheet->setCellValueByColumnAndRow(6,$i, $row['trouble_type2']);
    $sheet->setCellValueByColumnAndRow(7,$i, $row['circuit_number']);
    $sheet->setCellValueByColumnAndRow(8,$i, $row['trouble_symptom']);
    $sheet->setCellValueByColumnAndRow(9,$i, $row['start_time']);
    $sheet->setCellValueByColumnAndRow(10,$i, $row['end_time']);
    $sheet->setCellValueByColumnAndRow(11,$i, $row['time']);
    $sheet->setCellValueByColumnAndRow(12,$i, $row['net_duration']);
    $sheet->setCellValueByColumnAndRow(13,$i, $row['assessment_time']);
    $sheet->setCellValueByColumnAndRow(14,$i, $row['reduce_time']);
    $sheet->setCellValueByColumnAndRow(15,$i, $row['time_limit']);
    $sheet->setCellValueByColumnAndRow(16,$i, $row['time_out']=='1'?'是':'');
    $sheet->setCellValueByColumnAndRow(17,$i, $row['reason']);
    $sheet->setCellValueByColumnAndRow(18,$i, $row['trouble_reason_symptom']);
    $sheet->setCellValueByColumnAndRow(19,$i, $row['is_assess']=='1'?'是':'');
    $sheet->setCellValueByColumnAndRow(20,$i, $row['trouble_position']);
    $sheet->setCellValueByColumnAndRow(21,$i, $row['province']);
    $sheet->setCellValueByColumnAndRow(22,$i, $row['responsible_province']);
    $sheet->setCellValueByColumnAndRow(23,$i, $row['TOP33']=='1'?'是':'');
    $sheet->setCellValueByColumnAndRow(24,$i, $row['TOP160']=='1'?'是':'');
    $sheet->setCellValueByColumnAndRow(25,$i, $row['TOP210']=='1'?'是':'');
    $sheet->setCellValueByColumnAndRow(26,$i, $row['TOP800']=='1'?'是':'');
    $sheet->setCellValueByColumnAndRow(27,$i, $row['TOPN']=='1'?'是':'');
    $sheet->setCellValueByColumnAndRow(28,$i, $row['assess_TOPN']=='1'?'是':'');
    $sheet->setCellValueByColumnAndRow(29,$i, $row['handle_unit']);
    $sheet->setCellValueByColumnAndRow(30,$i, $row['major']);
    $sheet->setCellValueByColumnAndRow(31,$i, $row['is_trouble']=='1'?'是':'');
    $sheet->setCellValueByColumnAndRow(32,$i, $row['trouble_class']);
    $sheet->setCellValueByColumnAndRow(33,$i, $row['trouble_reason']);
    $sheet->setCellValueByColumnAndRow(34,$i, $row['area']);
    $sheet->setCellValueByColumnAndRow(35,$i, $row['roomName']);
    $sheet->setCellValueByColumnAndRow(36,$i, $row['roomType']);
    $sheet->setCellValueByColumnAndRow(37,$i, $row['reasonDescription']);
    $sheet->setCellValueByColumnAndRow(38,$i, $row['hiddenDanger']);
    $sheet->setCellValueByColumnAndRow(39,$i, $row['remark']);
    $sheet->setCellValueByColumnAndRow(40,$i, $row['correct_province']);

    $i++;
  }
  
  $writer = new Xlsx($spreadsheet);
  date_default_timezone_set('PRC');
  $d = date('Ymd',time());
  $saveName = $d.'-'.uniqid().'.xlsx';
  $writer->save('../../files/'.$saveName);
  
  $res = array("status" => "success","sum" => $i,"fileName" => $saveName);
  echo json_encode($res);
}
mysqli_close($conn);
?>