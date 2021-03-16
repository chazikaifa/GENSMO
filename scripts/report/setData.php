<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

if(!isset($_POST['data'])){
	exit('empty data');
}
$data = $_POST['data'];
$filename = './data.txt';
$f = fopen($filename,'w') or Die(json_encode(array("status" => "error","errMsg" => "File Open Fail")));
fwrite($f,$data);
fclose($f);
echo json_encode(array("status" => "success"));
?>