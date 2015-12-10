<?php
//每天凌晨00:01
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'tongji','RN}_IwoAGVYh5xYh]-=-');
$idc_dsn = 'mysql:host=localhost;port=3306;dbname=rht_idc';
$idc_conn = new PDO($idc_dsn,'root','password');
$predate = date('Y-m-d',strtotime('-1 days'));
$tempPredate = date('Ymd',strtotime('-1 days'));
$currentdate = date('Y-m-d');
$premiketime = strtotime($predate);
$curmiketime = strtotime($currentdate)-1;
//获取昨天 client
//$clientSql = 'SELECT DISTINCT client FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 802';
$clientSql = 'SELECT phoneno FROM rhi_lottery WHERE createtime BETWEEN '.$premiketime.' AND '.$curmiketime;
$macSql = 'SELECT * FROM rhc_hdclient';
$clientRs = $idc_conn->query($clientSql);
$clientRs->setFetchMode(PDO::FETCH_ASSOC);
$clientRows = $clientRs->fetchAll();

$macRs = $tongji_conn->query($macSql);
$macRs->setFetchMode(PDO::FETCH_ASSOC);
$macRows = $macRs->fetchAll();
$macArr = array();
foreach($macRows as $v){$macArr[] = strtoupper($v['client']);}
$insertSql = "INSERT INTO rhc_hdclient(client,cday,type) VALUES (?,?,?)";
//插入新用户的mac地址
foreach($clientRows as $v){
    $bind = array();
    if(!in_array(strtoupper($v['phoneno']),$macArr)){
        $tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bind = array(strtoupper($v['phoneno']),$predate,1);
        $stmt = $tongji_conn->prepare($insertSql);
        $stmt->execute($bind);
    }
}
$stmt = NULL;

 ?>