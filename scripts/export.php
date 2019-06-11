<?php
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();


header("Content-Type: text/html;charset=utf-8");
$id = $_POST['id'];
$name = $_POST['name'];
$start_time_start = $_POST['start_time_start'];
$start_time_end = $_POST['start_time_end'];
$end_time_start = $_POST['end_time_start'];
$end_time_end = $_POST['end_time_end'];
$number = $_POST['number'];
$index = $_POST['index'];
$limit = $_POST['limit'];
if(isset($_POST['step'])){
	$step = $_POST['step'];
}else{
	$step = "";
}
$dbhost = 'localhost';  // mysql服务器主机地址
$dbuser = 'root';            // mysql用户名
$dbpass = '';          // mysql用户名密码
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
	//$message = array("status" => "error","result" => mysqli_error());
	die(mysqli_error());
}
mysqli_query($conn , "set names utf8");
mysqli_select_db($conn,'GENSMO');

$condition = "";
if($id != ""){
	$condition .= "`id` LIKE '$id' AND " ;
}
if($name != ""){
	$condition .= "`name` LIKE '$name' AND ";
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
if($step != ""){
	$condition .="`step` LIKE '$step' AND ";
}
if($condition != ""){
	$condition = substr($condition,0,strlen($condition)-4);
	$condition = "WHERE ".$condition;
}
$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM `order` '. $condition .'ORDER BY `create_time` DESC LIMIT '.$index.','.$limit ;
//exit($sql);
$result = mysqli_query($conn, $sql);
if(!$result){
	//$message = array("status" => "error","result" => mysqli_error());
	die(mysqli_error($conn));
}else{
	$row_number = mysqli_fetch_row(mysqli_query($conn,"SELECT FOUND_ROWS()"))[0];
	
	if($row_number == 0){
		$res = array("status" => "fail","error_message" => "empty result");
		echo json_encode($res);
	}else{
		$sheet->setCellValueByColumnAndRow(1,1, '故障单编号');
		$sheet->setCellValueByColumnAndRow(2,1, '客户名称');
		$sheet->setCellValueByColumnAndRow(3,1, '故障受理时间');
		$sheet->setCellValueByColumnAndRow(4,1, '故障修复时间');
		$sheet->setCellValueByColumnAndRow(5,1, '故障历时(分钟)');
		$sheet->setCellValueByColumnAndRow(6,1, '工单状态');
		$sheet->setCellValueByColumnAndRow(7,1, '故障简述');
		$sheet->setCellValueByColumnAndRow(8,1, '19工单编号');
		$sheet->setCellValueByColumnAndRow(9,1, '故障进展');
		$sheet->setCellValueByColumnAndRow(10,1, '电路编号');
		$sheet->setCellValueByColumnAndRow(11,1, '客户联系方式');
		$sheet->setCellValueByColumnAndRow(12,1, '客户联系人');
		$sheet->setCellValueByColumnAndRow(13,1, '区域');
		$sheet->setCellValueByColumnAndRow(14,1, '是否故障');
		$sheet->setCellValueByColumnAndRow(15,1, '是否对端');
		$sheet->setCellValueByColumnAndRow(16,1, '故障分类');
		$sheet->setCellValueByColumnAndRow(17,1, '原因细化');
		$sheet->setCellValueByColumnAndRow(18,1, '行业类型');
		$sheet->setCellValueByColumnAndRow(19,1, '备注');
		
		$i = 2;
		
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
			$sheet->setCellValueByColumnAndRow(1,$i, $row['id']);
			$sheet->setCellValueByColumnAndRow(2,$i, $row['name']);
			$sheet->setCellValueByColumnAndRow(3,$i, $row['start_time']);
			$sheet->setCellValueByColumnAndRow(4,$i, $row['end_time']);
			$sheet->setCellValueByColumnAndRow(5,$i, $row['time']);
			$sheet->setCellValueByColumnAndRow(6,$i, $row['step']);
			$sheet->setCellValueByColumnAndRow(7,$i, $row['trouble_symptom']);
			$sheet->setCellValueByColumnAndRow(8,$i, $row['link_id']);
			$sheet->setCellValueByColumnAndRow(9,$i, $row['process']);
			$sheet->setCellValueByColumnAndRow(10,$i, $row['circuit_number']);
			$sheet->setCellValueByColumnAndRow(11,$i, $row['contact_number']);
			$sheet->setCellValueByColumnAndRow(12,$i, $row['contact_name']);
			$sheet->setCellValueByColumnAndRow(13,$i, $row['area']);
			$sheet->setCellValueByColumnAndRow(14,$i, $row['is_trouble']);
			$sheet->setCellValueByColumnAndRow(15,$i, $row['is_remote']);
			$sheet->setCellValueByColumnAndRow(16,$i, $row['trouble_class']);
			$sheet->setCellValueByColumnAndRow(17,$i, $row['trouble_reason']);
			$sheet->setCellValueByColumnAndRow(18,$i, $row['business_type']);
			$sheet->setCellValueByColumnAndRow(19,$i, $row['remark']);
			$i++;
		}
		
		$writer = new Xlsx($spreadsheet);
		$saveName = uniqid().'.xlsx';
		$writer->save('../files/'.$saveName);
		
		$res = array("status" => "success","sum" => $row_number,"fileName" => $saveName);
		echo json_encode($res);
	}
}
mysqli_close($conn);
?>