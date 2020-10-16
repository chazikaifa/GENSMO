<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

$arr = [
	// ["name" => "last3_text","data" => '8月21日-28日',"dataType" => 'string'],
	// ["name" => "last2_text","data" => '8月1日-11日',"dataType" => 'string'],
	// ["name" => "last_text","data" => '9月11日-18日',"dataType" => 'string'],
	["name" => "last3_text","data" => '6月',"dataType" => 'string'],
	["name" => "last2_text","data" => '7月',"dataType" => 'string'],
	["name" => "last_text","data" => '8月',"dataType" => 'string'],
	["name" => "assess_data_last3","data" => '596',"dataType" => 'number'],
	["name" => "assess_data_last2","data" => '592',"dataType" => 'number'],
	["name" => "assess_data_last","data" => '588',"dataType" => 'number'],
	["name" => "local_data_last3","data" => '298',"dataType" => 'number'],
	["name" => "local_data_last2","data" => '269',"dataType" => 'number'],
	["name" => "local_data_last","data" => '318',"dataType" => 'number'],
	["name" => "assess_duty_list_last","data" => [326,183,314,327,349,254,320,365,328,324,326,372],"dataType" => 'array'],
	["name" => "assess_duty_list","data" => [161,87,205,220,243,191,218,191,197],"dataType" => 'array'],
	["name" => "TOP55_list_last","data"=>[11,19,21,17,21,11,77,13,9,25,3],"dataType" => 'array'],
	["name" => "TOP55_time_list_last","data"=>[3836.1333,2570.6,2987.6167,1804.7,3047.0667,1465.1833,609.1167,1229.45,1075.45,828.9333,1875.717,243.2167],"dataType" => 'array'],
	["name" => "TOP55_list","data"=>[11,1,9,8,7,4,4,7,14],"dataType" => 'array'],
	["name" => "TOP55_time_list","data"=>[1313.1,13.2833,1194.25,710.3333,1192.65,444.9667,327.1,542.4,1330.2833],"dataType" => 'array'],
];
echo json_encode($arr);
//204,112
//112,69
//

//592,269
//588,318
//663,305
?>