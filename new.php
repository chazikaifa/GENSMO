<html>
<head>
	<meta charset="UTF-8">
<?php
$dbhost = 'localhost';  // mysql服务器主机地址
$dbuser = 'root';            // mysql用户名
$dbpass = '';          // mysql用户名密码
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
    die('Could not connect: ' . mysqli_error());
}
mysqli_query($conn , "set names utf8");
mysqli_select_db($conn,'GENSMO');

$param_name = array();
$param_name[0] = 'id';
$param_name[1] = 'name';
$param_name[2] = 'start_time';
$param_name[3] = 'end_time';
$param_name[4] = 'time';
$param_name[5] = 'step';
$param_name[6] = 'trouble_symptom';
$param_name[7] = 'link_id';
$param_name[8] = 'process';
$param_name[9] = 'circuit_number';
$param_name[10] = 'contact_number';
$param_name[11] = 'contact_name';
$param_name[12] = 'area';
$param_name[13] = 'is_trouble';
$param_name[14] = 'is_remote';
$param_name[15] = 'trouble_class';
$param_name[16] = 'trouble_reason';
$param_name[17] = 'business_type';
$param_name[18] = 'remark';

$param = array();
$param[$param_name[0]] = 'B0090401-0002';
$param[$param_name[1]] = '中国农业银行股份有限公司广州天河支行';
$param[$param_name[2]] = '2019-04-01 11:12:29';
$param[$param_name[3]] = '2019-04-01 15:01:11';
$param[$param_name[4]] = '3.811666667';
$param[$param_name[5]] = '结单';
$param[$param_name[6]] = '中断';
$param[$param_name[7]] = '';
$param[$param_name[8]] = '已通知传输处理2019-4-1 12:12:43张嘉乐反馈：正在处理2019-4-1 15:01:14罗浩反馈：沿河涌线槽里光缆被老鼠咬断多处，重新放缆200多米恢复';
$param[$param_name[9]] = '迎宾路中山大道NE0001NP';
$param[$param_name[10]] = '13922452569';
$param[$param_name[11]] = '黄利山';
$param[$param_name[12]] = '天河';
$param[$param_name[13]] = 1;
$param[$param_name[14]] = 0;
$param[$param_name[15]] = '光缆故障';
$param[$param_name[16]] = '老鼠咬断';
$param[$param_name[17]] = '金融、保险业';
$param[$param_name[18]] = '';

$sql = 'INSERT INTO `order` (';

foreach($param_name as $name){
	$sql .= '`'.$name.'`,';
}

$sql = substr($sql,0,strlen($sql)-1);
$sql .= ') VALUES (';

foreach($param as $p){
	$sql .= "'".$p."',";
}
$sql = substr($sql,0,strlen($sql)-1);
$sql .= ')';

$result = mysqli_query($conn, $sql);
if(!$result){
	die('error:'.mysqli_error($conn));
}
echo 'success';

mysqli_close($conn);
?>
</head>
</html>
