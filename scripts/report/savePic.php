<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

if(!isset($_POST['baseimg'])){
	exit('no image');
}
$picInfo = $_POST['baseimg'];
$streamFileRand = date('YmdHis').rand(1000,9999); //图片名
$picType ='.png';//图片后缀
$streamFilename = "./".$streamFileRand .$picType; //图片保存地址
preg_match('/(?<=base64,)[\S|\s]+/',$picInfo,$picInfoW);//处理base64文本
file_put_contents($streamFilename,base64_decode($picInfoW[0]));//文件写入
echo($streamFilename);
?>