<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

include('../system/canDo.php');
include('../system/DB.php');

if(isset($_POST['token'])){
  $token = $_POST['token'];
  if(is_attack($token)){
    $res = array("status" => "error","errMsg" => 'Token Unavailable');
    exit(json_encode($res));
  }
}else{
  $res = array("status" => "error","errMsg" => 'Token Unavailable');
  exit(json_encode($res));
}

$conn = LinkDB();
if(! $conn ){
  $res = array("status" => "error","errMsg" => 'Mysql Error: '.mysqli_error($conn));
  exit(json_encode($res));
}

$do = canDo($token,'uploadCustomer',$conn);
if($do['status'] != 'success'){
  $res = array("status" => "error","errMsg" => $do['errMsg']);
  exit(json_encode($res));
}

require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function getExeName($fileName){
  $path = explode('.',$fileName);
  if(count($path)>1){
    $exename = $path[count($path)-1];
  }else{
    $exename = '';
  }
  return strtolower($exename);
}
function getSQL($titles, $values){
  $sql = 'INSERT INTO `customer` (`id`,';
  if(sizeof($titles) == 0 || sizeof($values) == 0 || sizeof($titles) != sizeof($values)){
    return '';
  }
  foreach($titles as $title){
    if($title != "pass"){
      $sql .= "`$title`,";
    }
  }
  $sql = substr($sql,0,strlen($sql)-1);
  $sql .= ') VALUES (NULL,';
  for($i = 0;$i < sizeof($titles);$i++){
    if($titles[$i] != "pass"){
      if($titles[$i] == 'unify_name'){
        if($values[$i] == ''){
          return '';
        }else{
          $values[$i] = str_replace("\n", "", $values[$i]);
          $values[$i] = str_replace("\t", "", $values[$i]);
          $values[$i] = str_replace("\r", "", $values[$i]);
        }
      }
      if($titles[$i] == 'name'){
        $values[$i] = str_replace("\n", "", $values[$i]);
        $values[$i] = str_replace("\t", "", $values[$i]);
        $values[$i] = str_replace("\r", "", $values[$i]);
      }
      $v = $values[$i];
      if(!is_null($v)){
        $sql .= "'".$v."',";
      }else{
        $sql .= "NULL,";
      }
    }
  }
  $sql = substr($sql,0,strlen($sql)-1);
  $sql .= ');';
  return $sql;
}

if(!empty($_FILES['file'])){
  if($_FILES['file']['size'] > 5*1024*1024){
    $res = array("status" => "error","errMsg" => 'File Too Large');
    exit(json_encode($res));
  }
  $exename = getExeName($_FILES['file']['name']);
  //检验后缀
  if($exename == 'xls'){
    
  }else if($exename == 'xlsx'){
    
  }else{
    $res = array("status" => "error","errMsg" => 'Wrong File Type');
    exit(json_encode($res));
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
    $title = array();
    while($titles->valid()){
      $t = $titles -> current();
      $title[] = $t -> getFormattedValue();
      $titles->next();
    }
    $n = 0;
    $unify_index = -1;
    $name = array();
    foreach($title as $t){
      switch($t){
        case '统一名称':
          $name[] = 'unify_name';
          $unify_index = $n;
          break;
        case '客户名称':
          $name[] = 'name';
          break;
        case '等级':
          $name[] = 'level';
          break;
        case '标记设置':
          $name[] = 'pass';
          break;
        case '标记内容':
          $name[] = 'pass';
          break;
        case '标记':
          $name[] = 'mark';
          break;  
        case '网络经理':
          $name[] = 'N_Manager';
          break;
        case '网络经理电话':
          $name[] = 'NM_phone';
          break;
        case '客户经理':
          $name[] = 'C_Manager';
          break;
        case '客户经理电话':
          $name[] = 'CM_phone';
          break;
        case '来源':
          $name[] = 'origin';
          break;
        case '备注':
          $name[] = 'remark';
          break;
        case '':
          $name[] = 'pass';
          break;
        default:
          exit(json_encode(array("status"=>"fail","errMsg"=>"Title Error")));
      }
      $n++;
    }
    if($unify_index < 0){
      exit(json_encode(array("status"=>"fail","errMsg"=>"Title Error")));
    }
    
    $sql = "";
    $sum = 0;
    $empty_count = 0;
    while($rows -> valid() && $empty_count < 3){
      $sum++;
      $row = $rows -> current();
      $cells = $row -> getCellIterator();
      $values = array();
      $cell_index = 0;
      while($cells -> valid()){
        $cell = $cells -> current();
        $v = $cell -> getFormattedValue();
        if($cell_index == $unify_index && $v == ''){
          $empty_count++;
          $sum--;
          break;
        }else{
          $empty_count = 0;
        }
        $values[] = $v;
        $cell_index++;
        $cells -> next();
      }
      $sql .= getSQL($name,$values);
      $rows -> next();
    }
    
    //exit($sql);
    $result = mysqli_multi_query($conn, $sql);
    if(!$result){
      exit(json_encode(array("status"=>"fail","errMsg"=>"Mysql Error: ".mysqli_error($conn))));
    }else{
      echo json_encode(array("status"=>"success","sum"=>$sum)); 
    }     
    unlink($SavePath);
  }else{
    exit(json_encode(array("status"=>"fail","errMsg"=>"Save File Fail")));
  }
}else{
  echo json_encode(array("status"=>"fail","errMsg"=>"Empty Files"));
}
?>