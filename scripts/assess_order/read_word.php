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

date_default_timezone_set('PRC');
$d = date('Ymd',time());
$saveName = $d.'-'.uniqid().'.docx';
$savePath = '../../files/'.$saveName;

$JSON_ERROR = array('JSON_ERROR_NONE','JSON_ERROR_DEPTH','JSON_ERROR_STATE_MISMATCH','JSON_ERROR_CTRL_CHAR','JSON_ERROR_SYNTAX','JSON_ERROR_UTF8');
if(isset($_POST['DATA'])){
	$DATA = $_POST['DATA'];
	$json = json_decode($DATA,true);
	if(!$json){
		$res = array('status' => 'error','errMsg' => json_last_error_msg());
		exit(json_encode($res));
	}
}else{
	$res = array("status" => 'error','errMsg' => 'data not set');
	exit(json_encode($res));
}

if(isset($_POST['type'])){
	switch ($_POST['type']) {
		case 'week':
			$path = './week.docx';
			break;
		case 'month':
			$path = './month.docx';
			break;
		default:
			$path = './week.docx';
			break;
	}
}else{
	$path = './week.docx';
}

$img_arr = array();
$tp = new TemplateProcessor($path);
foreach($json as $item){
	if($item['type'] == 'text'){
		$tp->setValue($item['name'],$item['value']);
	}
	if($item['type'] == 'img' && $item['value'] != ''){
		$tp->setImageValue($item['name'],$item['value']);
		$img_arr[]=$item['value']['path'];
	}
	if($item['type'] == 'array' && count($item['value']) > 0){
		$tp->cloneRowAndSetValues($item['name'],$item['value']);
	}
}

$tp->saveAs($savePath);
foreach($img_arr as $path){
	unlink($path);
}
$res = array("status" => 'success','name' => $saveName);
echo json_encode($res);

// $word = IOFactory::load($path);
// $src = $word->getSections();
// foreach($src as $s){
// 	$elements = $s->getElements();
// 	$arr = GetElement($elements);
// 	if(!empty($arr)){
// 		foreach ($arr as $text) {
// 			//var_dump($text);
// 		}
// 	}else{
// 		//var_dump($arr);
// 	}
// }

// function GetElement($elements)
// {
// 	$arrx=[];
// 	foreach ($elements as $k=>$e1){
// 		// 获取word对象中对应内容类型类的节点的类名
// 		$class = get_class($e1);
// 		$class = explode('\\', $class)[3];
// 		if ($class=='Table'){
// 			// 获取最大行
// 			$rows=count($e1->getRows());

// 			// 获取最大列
// 			$cells=$e1->countColumns();

// 			// $arrx[$k]['rows']=$rows;
// 			// $arrx[$k]['cells']=$cells;

// 			// 循环获取对应行和列下的单元格的文本内容
// 			for($i=0;$i<$rows;$i++){
// 				// 获取对应行
// 				$rows_a=$e1->getRows()[$i];
// 				if($i == 1){
// 					$cells = $rows_a -> getCells();
// 					foreach($cells as $cell){
// 						$inE = $cell->getElement(0);
// 						//echo get_class($inE)."<BR/>";
// 						//echo $inE->getText();
// 						var_dump($inE);
// 					}
// 					echo "<br/>";
// 				}
// 				// for($j = 0; $j < $cells; $j++){
// 				// 	// 获取对应列
// 				// 	$x=$rows_a->getCells()[$j];
// 				// 	$arrx[$k]['text'][$i+1][$j+1]=getTextElement($x);
// 				// }
// 			}
// 		}
// 		// if($class == 'TextRun'){
// 		// 	$arrx[$k] = getTextElement($e1);
// 		// }
// 		// if($class == 'TextBreak'){
// 		// 	echo "<br/>";
// 		// }
// 	}    
// 	return $arrx;
// }    

// //获取文本的节点
// function getTextElement($E)
// {
// 	if($E == null){
// 		return;
// 	}
// 	$elements = $E->getElements();
// 	$xas='';
// 	$result = [];
// 	$inResult=[];
// 	$text=[];

// 	foreach($elements as $inE){
// 	    $ns = get_class($inE);
// 	    $elName = explode('\\', $ns)[3];

// 	    if($elName == 'Text'){
// 	        $result[] = textarr($inE);
// 	    }
// 	    elseif (method_exists($inE, 'getElements')){
// 	        $inResult = getTextElement($inE);
// 	    }

// 	    if(!is_null($inResult)){
// 	        $result = array_merge($result, $inResult);
// 	    }
// 	}
// 	return count($result) > 0 ? $result : null;
// }      

// //获取文本
// function textarr($e)
// {
// 	$textArr['text']=$e->getText();
// 	echo $textArr['text'];
// 	return $textArr;
// }   
?>