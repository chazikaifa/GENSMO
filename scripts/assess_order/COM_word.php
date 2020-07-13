<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

$word = new COM("word.application") or die("can NOT open word!");
echo "Loadind Word,v.{$word->Version}";
$word->Visible = 0;

$word->Documents->OPen(dirname(__FILE__)."/test.docx");
var_dump($word);
// try{
// 	$test= $word->ActiveDocument->content->Text;
// 	echo $test;
// }catch(Exception $e){
// 	echo $e;
// }
$word -> Quit();
?>