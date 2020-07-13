<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

ini_set('memory_limit','256M');

require '../../vendor/autoload.php';
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

$path = './test.docx';
date_default_timezone_set('PRC');
$d = date('Ymd',time());
$saveName = $d.'-'.uniqid().'.docx';
$savePath = '../../files/'.$saveName;

$tp = new TemplateProcessor($path);
$item = array(
	"type" => 'array',
	"name" => 'assessUnicomLineItem',
	"value" => array(
		array(
			"assessUnicomLineItem" => '1',
			"assessUnicomLineItemSum" => 111,
			"assessUnicomLineItemPercent" => 45.67
		),
		array(
			"assessUnicomLineItem" => '2',
			"assessUnicomLineItemSum" => 222,
			"assessUnicomLineItemPercent" => 12.56
		),
	)
);
if($item['type'] == 'array' && count($item['value']) > 0){
	$tp->cloneRowAndSetValues($item['name'],$item['value']);
}


$tp->saveAs($savePath);
$res = array("status" => 'success','name' => $saveName);
echo json_encode($res);
?>