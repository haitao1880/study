<?php
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'tongji','RN}_IwoAGVYh5xYh]-=-');
$train_dsn = 'mysql:host=localhost;port=3306;dbname=rht_train';
$train_conn = new PDO($train_dsn,'root','password');
$predate = date('Y-m-d',strtotime('-1 days'));
$tempPredate = date('Ymd',strtotime('-1 days'));
$currentdate = date('Y-m-d');
$premiketime = strtotime($predate);
$curmiketime = strtotime($currentdate)-1;

//获取前一天 游戏礼包发放统计

$gameSql = 'SELECT id from rht_apps WHERE ispack = 1';
$gameRs = $train_conn->query($gameSql);
$gameRs->setFetchMode(PDO::FETCH_ASSOC);
$gameRows = $gameRs->fetchAll();
$deletePkSql = 'DELETE FROM rhc_package_daily WHERE day = "'.$predate.'"';
$stmt = $tongji_conn->prepare($deletePkSql);
$stmt->execute();
$stmt = NULL;
foreach($gameRows as $k=>$v){
    $tempsql = 'SELECT count(id) AS total_send_times FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND id = '.$v['id'].' AND type = 303';
    $tempRs = $tongji_conn->query($tempsql);
    $tempRs->setFetchMode(PDO::FETCH_ASSOC);
    $tempRow = $tempRs->fetch();
    $tempArr['game'] = $v['id'];
    $tempArr['send_times'] = intval($tempRow['total_send_times']);
    $insertSql = "INSERT INTO rhc_package_daily(day,send_times,game) VALUES (?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($predate,$tempArr['send_times'],$tempArr['game']);
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind);
    $stmt = NULL;
}

 
 
 ?>
