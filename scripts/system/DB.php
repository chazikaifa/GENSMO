<?php
function linkDB(){
  $dbhost = 'localhost';  // mysql服务器主机地址
  $dbuser = 'gensmo';            // mysql用户名
  $dbpass = 'SoSF701TmkYrGY8m';          // mysql用户名密码
  $conn = mysqli_connect($dbhost, $dbuser, $dbpass);
  if($conn){
    mysqli_query($conn , "set names utf8");
    mysqli_select_db($conn,'gensmo');
  }
  return $conn;
}

function is_attack($value){
  $attack = ["/'/","/\"/","/\*/","/\(/","/\)/",'/%/','/,/'];
  foreach ($attack as $word) {
    if(preg_match($word, $value)){
      return true;
    }
  }
  return false;
}
?>