<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

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
	new TitleItem('orderId',['故障单编号']),
	new TitleItem('step',['当前步骤']),
	new TitleItem('name',['客户名称']),
	new TitleItem('business_type',['行业类型']),
	new TitleItem('level',['客户级别']),
	new TitleItem('trouble_type',['故障业务类型（一级）']),
	new TitleItem('trouble_type2',['（三级）']),
	new TitleItem('circuit_number',['专线业务－故障电路编号','语音业务－电话号码','互联网业务－互联网专线号']),
	new TitleItem('trouble_symptom',['故障简述']),
	new TitleItem('trouble_description',['故障描述']),
	new TitleItem('start_time',['受理时间']),
	new TitleItem('end_time',['销障时间']),
	new TitleItem('time',['业务恢复历时（分钟）']),
	new TitleItem('net_duration',['业务恢复净历时（分钟）']),
	new TitleItem('reason',['故障原因']),
	new TitleItem('trouble_reason_symptom',['故障原因简述']),
	new TitleItem('trouble_position',['故障段落']),
	new TitleItem('province',['（地市）']),
	new TitleItem('handle_unit',['主要处理部门']),
];

$circuit_pass = ['待用户填写','无','客户无法提供','-','无法提供','','不清楚','用户无法提供'];

$customer_reason = ['客户线路','客户动力','客户设备'];
$trouble_class = ['光缆故障','设备故障','动力配套','电缆故障'];
$trouble_reason = [
	'光缆故障' => ['市政施工','河涌整治','三线整治','恶意剪线','车辆挂断','老鼠咬断','自然灾害','光缆劣化','尾纤松动'],
	'设备故障' => ['数据设备','接入设备','传输设备','交换设备','客户端联通设备'],
	'动力配套' => ['机房停电','机房电池','机房空调','基站停电','基站电池','基站空调','室分停电','室分电池','室分空调'],
	'电缆故障' => ['市政施工','河涌整治','三线整治','恶意剪线','车辆挂断','老鼠咬断','自然灾害','电缆劣化','电缆松动']
];
$handle_unit = [
	'运维传输组' => '传输',
	'运维交换组' => '交换',
	'运维数据组' => '数据',
	'政企云化专网维护室' => '政企云',
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
		if($title->title == 'orderId'){
			$cells->seek($title->index[0]);
			$order_id = $cells->current()->getFormattedValue();
			if($order_id == ''){
				return false;
			}
		}
		if($title->title == 'step'){
			$cells->seek($title->index[0]);
			$step = $cells->current()->getFormattedValue();
			if($step == '已撤销'){
				//已撤销的工单不导入
				$json['step'] = 'cancel';
				return $json;
			}else{
				continue;
			}
		}
		foreach ($title->index as $index) {
			$cells->seek($index);
			$v = $cells->current()->getFormattedValue();
			if($title->title == 'circuit_number'){
				if(!in_array($v, $circuit_pass)){
					$value = $v;
				}
			}else if($v != ''){
				$value = $v;
			}
		}
		$json[$title->title] = $value;
	}

	$json['name'] = trim($json['name']);
	if($json['trouble_position'] == '用户'){
		$json['is_assess'] = 0;
	}else{
		$json['is_assess'] = 1;
	}
	if($judge = judgeTrouble($json)){
		$json = $judge;
	}
	$json = judgeMajor($json);
	$json = is_TOPN($json);
	$json = judgeTimetLimit($json);
	return $json;
}

function judgeTrouble($json){
	global $customer_reason,$trouble_class,$trouble_reason;
	$reason = $json['reason'];
	$flag = preg_match_all('/(?<=\[)(.*?)(?=\])/', $reason, $matches);
	if($flag){
		foreach ($matches[0] as $match) {
			$split = explode('-', $match);
			if(count($split) > 2){
				continue;
			}else if(count($split) == 1){
				if($split[0] == '否'){
					$json['is_trouble'] = false;
					break;
				}
				if(in_array($split[0], $customer_reason)){
					$json['is_trouble'] = true;
					$json['trouble_class'] = $split[0];
					break;
				}
			}else{
				if(in_array($split[0], $trouble_class)){
					$json['is_trouble'] = true;
					$json['trouble_class'] = $split[0];
					if(in_array($split[1], $trouble_reason[$split[0]])){
						$json['trouble_reason'] = $split[1];
						break;
					}
				}
			}
		}
		return $json;
	}else{
		return false;
	}
}

function judgeMajor($json){
	global $handle_unit;
	$json['major'] = '';
	$unit_text = $json['handle_unit'];
	$units = explode(',', $unit_text);
	foreach ($units as $unit) {
		if(array_key_exists($unit, $handle_unit)){
			$json['major'] .= $handle_unit[$unit].',';
		}
	}
	if($json['major'] != ''){
		$json['major'] = substr($json['major'], 0, -1);
	}else{
		$json['major'] = '其他';
	}
	return $json;
}

function is_TOPN($json){
	$name = $json['name'];
	$post_data = array("name" => $name);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "localhost/GENSMO/scripts/assess_order/is_TOPN.php");
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$data = curl_exec($curl);
	curl_close($curl);
	$res = json_decode($data);
	if($res->status == 'success'){
		if($res->result == 'true'){
			$json['assess_TOPN'] = 1;
			$json['level'] = $res->level;
		}else{
			$json['assess_TOPN'] = 0;
		}
	}else{
		//error
	}
	return $json;
}

function new_order($json){
	$post_data = array("DATA" => json_encode($json));
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "localhost/GENSMO/scripts/assess_order/new_order.php");
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

function judgeTimetLimit($json){
	$trouble_type = $json['trouble_type'];
	$trouble_symptom = $json['trouble_symptom'];
	$TOPN = $json['assess_TOPN'];
	$level = $json['level'];
	$time = (int)$json['time'];
	$json['time'] = $time;
	if($TOPN){
		switch ($trouble_type) {
			case '语音业务':
				$json['time_limit'] = 480;
				break;
			case '互联网业务员':
				if($trouble_symptom == '不通'){
					$json['time_limit'] = 240;
				}else{
					$json['time_limit'] = 480;
				}
				break;
			case '专线业务':
				if($trouble_symptom == '不通'){
					if($level == '一级' || $level == '二级'){
						$json['time_limit'] = 120;
					}else{
						$json['time_limit'] = 240;
					}
				}else{
					$json['time_limit'] = 480;
				}
				break;
		}
	}else{
		if($trouble_type != '语音业务' && $trouble_symptom == '不通'){
			$json['time_limit'] = 240;
		}else{
			$json['time_limit'] = 480;
		}
	}
	if($json['time_limit'] < $time){
		$json['time_out'] = 1;
	}else{
		$json['time_out'] = 0;
	}
	return $json;
}

if(!empty($_FILES['file'])){
	if($_FILES['file']['size'] > 10*1024*1024){
		exit(json_encode(array("status"=>"fail","errMsg"=>"file too large")));
	}
	$exename = getExeName($_FILES['file']['name']);
	//检验后缀
	if($exename == 'xls'){
		
	}else if($exename == 'xlsx'){
		
	}else{
		exit(json_encode(array("status"=>"fail","errMsg"=>"wrong file type")));
	}
	
	$SavePath = "../../files/".uniqid().'.'.$exename;
	if(move_uploaded_file($_FILES['file']['tmp_name'],$SavePath)){
		//读取excel
		$spreadsheet = IOFactory::load($SavePath);
		$sheet = $spreadsheet -> getSheet(0);
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

		$sql = "";
		$sum = 0;
		$empty_count = 0;
		while($rows -> valid() && $empty_count < 3){
			$json = getJSON($rows,$title_msg);
			if($json){
				if(isset($json['step']) && $json['step'] == 'cancel'){
					//do nothing
				}else{
					$res = new_order($json);
					if($res->status == 'success'){
						$sum++;
					}else{
						echo $res->errMsg;
					}
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