<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

$arr = [
	["name" => "last3_text","data" => '7月10日-17日',"dataType" => 'string'],
	["name" => "last2_text","data" => '7月17日-24日',"dataType" => 'string'],
	["name" => "last_text","data" => '7月24日-31日',"dataType" => 'string'],
	// ["name" => "last3_text","data" => '4月',"dataType" => 'string'],
	// ["name" => "last2_text","data" => '5月',"dataType" => 'string'],
	// ["name" => "last_text","data" => '6月',"dataType" => 'string'],
	["name" => "assess_data_last3","data" => '136',"dataType" => 'number'],
	["name" => "assess_data_last2","data" => '133',"dataType" => 'number'],
	["name" => "assess_data_last","data" => '164',"dataType" => 'number'],
	["name" => "local_data_last3","data" => '57',"dataType" => 'number'],
	["name" => "local_data_last2","data" => '77',"dataType" => 'number'],
	["name" => "local_data_last","data" => '54',"dataType" => 'number'],
	["name" => "assess_duty_list_last","data" => [326,183,314,327,349,254,320,365,328,324,326,372],"dataType" => 'array'],
	["name" => "assess_duty_list","data" => [161,87,205,220,243,191,218],"dataType" => 'array'],
];
echo json_encode($arr);
//133,77
//164,54
//254,135

//622,353
//596,298
//592,269
?>