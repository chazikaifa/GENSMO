<?php
function canDo($token,$operation,$conn){
  $sql = "SELECT `groupID` FROM `user` WHERE `token` LIKE '$token' AND (`token_time`='2020-01-01 00:00:00' OR `token_time` > CURRENT_TIMESTAMP)";
  $result = mysqli_query($conn, $sql);
  if(!$result){
    return array("status" => 'error',"errMsg" => 'mysql error: '.mysqli_error($conn));
  }else{
    $row = mysqli_fetch_row($result);
    if($row == null){
      return array("status" => "error","errMsg" => 'Token Unavailable');
    }
    $user_group = $row[0];
    $sql = "SELECT `allow`,`deny` FROM `user_group` WHERE `groupID`='$user_group'";
    $result = mysqli_query($conn, $sql);
    if(!$result){
      return array("status" => 'error',"errMsg" => 'mysql error: '.mysqli_error($conn));
    }else{
      $row = mysqli_fetch_row($result);
      if($row == null){
        return array("status" => "error","errMsg" => 'Token Unavailable');
      }
      $allow = $row[0];
      $deny = $row[1];
      if($allow == 'all'&&!preg_match('/'.$operation.'/', $deny)){
        refreshToken($token,$conn);
        return array("status" => "success");
      }
      if(preg_match('/'.$operation.'/', $allow)){
        refreshToken($token,$conn);
        return array("status" => "success");
      }
      return array("status" => "error","errMsg" => 'Permission Deny');
    }
  }
}

function refreshToken($token,$conn){
  $sql = "UPDATE `user` SET `token_time`= DATE_ADD(CURRENT_TIMESTAMP,INTERVAL 12 HOUR) WHERE `token` LIKE '$token' and `token_time` != '2020-01-01 00:00:00'";
  $result = mysqli_query($conn, $sql);
  if(!$result){
    return array("status" => 'error',"errMsg" => 'mysql error: '.mysqli_error($conn));
  }else{
    return array("status" => "success");
  }
}
?>