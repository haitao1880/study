<?php
//每天凌晨00:01
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'tongji','RN}_IwoAGVYh5xYh]-=-');
$mac_dsn = 'mysql:host=localhost;port=3306;dbname=rht_admin';
$mac_conn = new PDO($mac_dsn,'root','password');
$predate = date('Y-m-d',strtotime('-1 days'));
$tempPredate = date('Ymd',strtotime('-1 days'));
$currentdate = date('Y-m-d');
$premiketime = strtotime($predate);
$curmiketime = strtotime($currentdate)-1;
//获取昨天 client
$clientSql = 'SELECT DISTINCT client FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 1 AND pid = 0';
$macSql = 'SELECT usermac FROM rha_open_trainapp';
$clientRs = $tongji_conn->query($clientSql);
$clientRs->setFetchMode(PDO::FETCH_ASSOC);
$clientRows = $clientRs->fetchAll();

$macRs = $mac_conn->query($macSql);
$macRs->setFetchMode(PDO::FETCH_ASSOC);
$macRows = $macRs->fetchAll();
$macArr = array();
foreach($macRows as $v){$macArr[] = strtoupper($v['usermac']);}
$insertSql = "INSERT INTO rha_open_trainapp(usermac,date,sys,type) VALUES (?,?,?,?)";
//插入新用户的mac地址
foreach($clientRows as $v){
    $bind = array();
    if(!in_array(strtoupper($v['client']),$macArr)){
        $mac_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $mac_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bind = array(strtoupper($v['client']),$predate,'','gameoppen');
        $stmt = $mac_conn->prepare($insertSql);
        $stmt->execute($bind);
    }
}
$stmt = NULL;

 ?>