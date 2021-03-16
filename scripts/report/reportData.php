<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

	// ["name" => "assess_duty_list_2019","data" => [326,183,314,327,349,254,320,365,328,324,326,372],"dataType" => 'array'],
	// ["name" => "TOP55_list_2019","data"=>[11,19,21,17,21,11,7,13,13,9,25,3],"dataType" => 'array'],
	// ["name" => "TOP55_time_list_2019","data"=>[3836.1333,2570.6,2987.6167,1804.7,3047.0667,1465.1833,609.1167,1229.45,1075.45,828.9333,1875.717,243.2167],"dataType" => 'array'],
	
// $arr = [
	// // ["name" => "last3_text","data" => '1月15日-22日',"dataType" => 'string'],
	// // ["name" => "last2_text","data" => '1月22日-29日',"dataType" => 'string'],
	// // ["name" => "last_text","data" => '2月1日-19日',"dataType" => 'string'],
	// ["name" => "last3_text","data" => '11月',"dataType" => 'string'],
	// ["name" => "last2_text","data" => '12月',"dataType" => 'string'],
	// ["name" => "last_text","data" => '1月',"dataType" => 'string'],
	// ["name" => "assess_data_last3","data" => '642',"dataType" => 'number'],
	// ["name" => "assess_data_last2","data" => '673',"dataType" => 'number'],
	// ["name" => "assess_data_last","data" => '647',"dataType" => 'number'],
	// ["name" => "local_data_last3","data" => '262',"dataType" => 'number'],
	// ["name" => "local_data_last2","data" => '383',"dataType" => 'number'],
	// ["name" => "local_data_last","data" => '277',"dataType" => 'number'],
	// ["name" => "assess_duty_list_last","data" => [161,87,205,220,243,191,218,191,197,210,214,254],"dataType" => 'array'],
	// ["name" => "assess_duty_list","data" => [168],"dataType" => 'array'],
	// ["name" => "TOP55_list_last","data"=>[11,1,9,8,7,4,4,7,14,23,24,20],"dataType" => 'array'],
	// ["name" => "TOP55_time_list_last","data"=>[1313.1,13.2833,1194.25,710.3333,1192.65,444.9667,327.1,542.4,1330.2833,2116.2334,2018.05,1381.2167],"dataType" => 'array'],
	// ["name" => "TOP55_list","data"=>[16,9],"dataType" => 'array'],
	// ["name" => "TOP55_time_list","data"=>[1345.6,973.8167],"dataType" => 'array'],
// ];
// echo json_encode($arr);
//184,69
//175,76
//95,41

//673,383
//647,277

$filename = './data.txt';
$f = fopen($filename,'r');
$res = fread($f, filesize($filename));
fclose($f);
echo $res;

?>