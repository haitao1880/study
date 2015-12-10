<?php 

header("Content-type: text/html; charset=utf-8");

$dsn = "mysql:host=localhost;dbname=rht_admin";
$db = new PDO($dsn, 'root', 'password');

$fname = '/etc/openvpn/openvpn-status.log';
$data = file($fname);
$arr = array();
foreach ($data as $key=>$var){
	$k = explode(',', str_replace(PHP_EOL, '', $var));
	if (!is_array($k) || end($k) == 'Connected Since' || end($k) == 'Last Ref' || count($k)<4) {
		continue;
	}
	array_push($arr, $k);
}

$ser1 = 'select ipcno from rha_ipcstatus';
$s = $db->query($ser1);
$s->setFetchMode(PDO::FETCH_ASSOC);
$res=$s->fetchAll();

//组合成IPCS
$ipcs = array();
foreach ($res as $m=>$n){
	array_push($ipcs, $n['ipcno']);
}

$onlines =array();
foreach ($arr as $ke => $va){
	if (count($va) == 5){
		$int = array(
			$va[1], 	//'ip'
			$va[2],		//'rbyte'	
			$va[3], 	//'sbyte'	
			strtotime($va[4]), //'ctime'
			1,			//online	
			$va[0]		//'ipcno'	
		);
		if (in_array($va[0], $ipcs)) {
			$sql2 = 'Update rha_ipcstatus set ip = ?,rbyte = ?,sbyte = ?, ctime = ?, online = ? where ipcno = ?';
			$sth2 = $db->prepare($sql2);
			$sth2->execute($int);
			array_push($onlines, $va[0]);
		}else{
			$sql3 = 'Insert Into rha_ipcstatus(ip,rbyte,sbyte,ctime,online,ipcno) values (?,?,?,?,?,?)';
			$sth3 = $db->prepare($sql3);
			$sth3->execute($int);
		}
	}else{
		$sql4 = 'Update rha_ipcstatus set vip = ?,lastref = ?,online = 1 where ipcno = ?';
		$sth4 = $db->prepare($sql4);
		$sth4->execute(array($va[0],strtotime($va[3]),$va[1]));
	}
}
var_dump($onlines);

//修改未在线状态
$offlines = array_diff($ipcs,$onlines);
if (count($offlines) < 1) {
	return ;
}
$sql5 = 'Update rha_ipcstatus set online = 0 where online = 1 and ipcno in(';
foreach ($offlines as $a=>$b){
	$sql5 .= "'$b',";
}
$sql5 = rtrim($sql5,',');
$sql5 .= ')';
$L = $db->query($sql5);

?>