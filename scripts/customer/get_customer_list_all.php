<?php
header("Content-Type: text/html;charset=utf-8");
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

include('../system/canDo.php');
include('../system/DB.php');

$conn = LinkDB();
if(! $conn ){
  $res = array("status" => "error","errMsg" => 'Mysql Error: '.mysqli_error($conn));
  exit(json_encode($res));
}

$sql = 'SELECT  * FROM `customer` ORDER BY `update_date` DESC' ;
$result = mysqli_query($conn, $sql);
if(!$result){
  $res = array("status" => "error","errMsg" => 'Mysql Error: '.mysqli_error($conn));
  exit(json_encode($res));
}else{
  $resList = array();
  $customer = [];
  $params = ['level','N_manager','NM_phone','C_manager','CM_phone'];
  while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
    //$resList[$i] = $row;
    if(isset($customer[$row['name']])){
      foreach ($params as $p) {
        if($row[$p] != ''){
          $customer[$row['name']][$p] = $row[$p];
        }
      }
      $marks = explode(' ', $row['mark']);
      $mark = explode('|', $customer[$row['name']]['mark']);
      if(count($marks) > 1){
        if($marks[0] == 'SET'){
          $flag = false;
          foreach ($mark as $m) {
            if($m == $marks[1]){
              $flag = true;
            }
          }
          if(!$flag){
            $mark []= $marks[1];
          }
        }else{
          for($i=0;$i<count($mark);$i++) {
            if($mark[$i] == $marks[1]){
              array_splice($mark, $i);
              break;
            }
          }
        }
        $mark_text = '';
        foreach ($mark as $m) {
          $mark_text .= $m.'|';
        }
        if($mark_text != ''){
          $mark_text = substr($mark_text,0,strlen($mark_text)-1);
        }
        $customer[$row['name']]['mark'] = $mark_text;
      }
    }else{
      $marks = explode(' ', $row['mark']);
      if(count($marks) > 1){
        if($marks[0] == 'SET'){
          $row['mark'] = $marks[1];
        }
      }
      $customer[$row['name']] = $row;
    }
  }
  foreach ($customer as $c) {
    $sql = 'INSERT INTO `customer_tmp` 
      (
        id,
        unify_name,
        name,
        mark,
        level,
        N_manager,
        NM_phone,
        C_manager,
        CM_phone,
        update_date,
        origin,
        remark
      ) VALUES ('.
        '\''.$c['id'].'\','.
        '\''.$c['unify_name'].'\','.
        '\''.$c['name'].'\','.
        '\''.$c['mark'].'\','.
        '\''.$c['level'].'\','.
        '\''.$c['N_manager'].'\','.
        '\''.$c['NM_phone'].'\','.
        '\''.$c['C_manager'].'\','.
        '\''.$c['CM_phone'].'\','.
        '\''.$c['update_date'].'\','.
        '\''.$c['origin'].'\','.
        '\''.$c['remark'].'\''.
      ')';
    $result = mysqli_query($conn, $sql);
    if(!$result){
      $res = array("status" => "error","errMsg" => 'Mysql Error: '.mysqli_error($conn));
      echo(json_encode($res));
    }
  }
}
mysqli_close($conn);
?>