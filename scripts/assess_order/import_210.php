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
  new TitleItem('orderId',['故障单编号']),
  new TitleItem('net_duration',['故障处理净历时']),
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
  global $title_msg;
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
    foreach ($title->index as $index) {
      $cells->seek($index);
      $v = $cells->current()->getFormattedValue();
      if($v != ''){
        $value = $v;
      }else{
        $value = '0';
      }
    }
    $json[$title->title] = $value;
  }
  return $json;
}

function update_210($json){
  $post_data = array("DATA" => json_encode($json));
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, "localhost/GENSMO/scripts/assess_order/update_210.php");
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
  if($_FILES['file']['size'] > 2*1024*1024){
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
    $sheet = $spreadsheet -> getSheetByName('广东省');
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
    $json_arr = array("sum"=>0,"data"=>array(),"title"=>["orderId","net_duration"]);
    while($rows -> valid() && $empty_count < 3){
      $json = getJSON($rows,$title_msg);
      if($json){
        $json_arr['data'] []= $json;
        $sum++;
      }else{
        $empty_count++;
      }
      $rows -> next();
    }
    $json_arr["sum"] = $sum;
    $res = update_210($json_arr);
    if($res->status == 'success'){
      echo json_encode(array("status"=>"success","sum"=>$sum));
    }else{
      echo json_encode(array("status"=>"error","errMsg"=>$res->errMsg));
    }
    unlink($SavePath);
  }else{
    exit(json_encode(array("status"=>"fail","errMsg"=>"save file fail")));
  }
}else{
  echo json_encode(array("status"=>"fail","errMsg"=>"empty files"));
}
?>