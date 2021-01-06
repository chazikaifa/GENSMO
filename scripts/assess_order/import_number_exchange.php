<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

ini_set('memory_limit','256M');

require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$dbhost = 'localhost';  // mysql服务器主机地址
$dbuser = 'gensmo';            // mysql用户名
$dbpass = 'SoSF701TmkYrGY8m';          // mysql用户名密码

class TitleItem{
	public $title_CN = [];
	public $index = [];
	public $title;
	function __construct($title,$title_CN=[]){
		$this->title_CN = $title_CN;
		$this->title = $title;
		$this->index = [];
	}
}

$title_msg = [
	new TitleItem('circuit_number',['电路编号']),
	new TitleItem('product_number',['BSS计费号码']),
	new TitleItem('route',['路由']),
	new TitleItem('name',['客户名称'])
];

function getExeName($fileName){
	$path = explode('.',$fileName);
	if(count($path)>1){
		$exename = $path[count($path)-1];
	}else{
		$exename = '';
	}
	return strtolower($exename);
}

function checkIndex(){
	global $title_msg;
	foreach ($title_msg as $title) {
		if(count($title->index) == 0){
			exit(json_encode(array("status"=>"fail","errMsg"=>$title->title." index error")));
		}
	}
}

function getJSON($rows){
	global $title_msg,$circuit_pass;
	$json = array();
	$row = $rows -> current();
	$cells = $row -> getCellIterator();
	foreach ($title_msg as $title) {
		$value = '';
		foreach ($title->index as $index) {
			$cells->seek($index);
			$v = $cells->current()->getFormattedValue();
			$value = str_replace('[', '', $v);
			$value = str_replace(']', '', $value);
			$value = str_replace('\\', '\\\\', $value);
			$value = str_replace('\'', '\\\'', $value);
		}
		$json[$title->title] = $value;
	}
	return $json;
}

function new_order($json){
	$post_data = array("DATA" => json_encode($json));
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "localhost/GENSMO/scripts/assess_order/new_circuit_number.php");
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$data = curl_exec($curl);
	curl_close($curl);
	$res = json_decode($data);
	return $res;
}

if(!empty($_FILES['file'])){
	if($_FILES['file']['size'] > 5*1024*1024){
		exit(json_encode(array("status"=>"fail","errMsg"=>"file too large")));
	}
	$exename = getExeName($_FILES['file']['name']);
	//检验后缀
	if($exename == 'xls'){
		
	}else if($exename == 'xlsx'){
		
	}else{
		exit(json_encode(array("status"=>"fail","errMsg"=>"wrong file type")));
	}
	
	if(isset($_POST['skip'])){
		$skip = $_POST['skip'];
	}else{
		$skip = 0;
	}
	$SavePath = "../../files/".uniqid().'.'.$exename;
	if(move_uploaded_file($_FILES['file']['tmp_name'],$SavePath)){
		//读取excel
		$spreadsheet = IOFactory::load($SavePath);
		$sheet = $spreadsheet -> getSheetByName('融合电路台账');
		$rows = $sheet -> getRowIterator();
		$titleRow = $rows -> current();
		$rows -> next();
		$titles = $titleRow -> getCellIterator();
		while($titles->valid()){
			$t = $titles -> current();
			$title = $t -> getFormattedValue();
			//标题中含有换行，影响判断
			$title = str_replace("\n", "", $title);
			foreach ($title_msg as  $title_obj) {
				foreach ($title_obj->title_CN as $title_CN) {
					if($title_CN == $title){
						$title_obj->index []= $titles->key();
					}
				}
			}
			$titles->next();
		}
		
		checkIndex($title_msg);

		$sum = $skip;
		$empty_count = 0;
		$rows -> seek($skip+1);
		while($rows -> valid() && $empty_count < 3){
			$json = getJSON($rows);
			if($json){
				$res = new_order($json);
				if($res->status == 'success'){
					$sum++;
					echo $sum.'<br/>';
				}else{
					echo $res->errMsg;
				}
			}else{
				$empty_count++;
			}
			$rows -> next();
		}
		echo json_encode(array("status"=>"success","sum"=>$sum));		
		unlink($SavePath);
	}else{
		exit(json_encode(array("status"=>"fail","errMsg"=>"save file fail")));
	}
}else{
	echo json_encode(array("status"=>"fail","errMsg"=>"empty files"));
}
?>