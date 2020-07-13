<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

$arr = [
	["name" => "last3_text","data" => '5月22日-29日',"dataType" => 'string'],
	["name" => "last2_text","data" => '6月1日-11日',"dataType" => 'string'],
	["name" => "last_text","data" => '6月12日-18日',"dataType" => 'string'],
	["name" => "assess_data_last3","data" => '178',"dataType" => 'number'],
	["name" => "assess_data_last2","data" => '223',"dataType" => 'number'],
	["name" => "assess_data_last","data" => '121',"dataType" => 'number'],
	["name" => "local_data_last3","data" => '84',"dataType" => 'number'],
	["name" => "local_data_last2","data" => '109',"dataType" => 'number'],
	["name" => "local_data_last","data" => '60',"dataType" => 'number'],
	["name" => "assess_duty_list_last","data" => [326,183,314,327,349,254,320,365,328,324,326,372],"dataType" => 'array'],
	["name" => "assess_duty_list","data" => [161,87,205,220,243,191],"dataType" => 'array'],
];
echo json_encode($arr);
//178,84
//223,109

//491,285
//559,336
//622,353
?>