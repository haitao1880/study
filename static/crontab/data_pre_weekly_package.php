<?php
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'tongji','RN}_IwoAGVYh5xYh]-=-');
$train_dsn = 'mysql:host=localhost;port=3306;dbname=rht_train';
$train_conn = new PDO($train_dsn,'root','password');
$predate = date('Y-m-d');
$tempdate = date('Y-m-d',strtotime("$predate Monday"));
$startday = date('Y-m-d',strtotime("$tempdate -14 days"));
$endday = date('Y-m-d',strtotime("$tempdate -8 days"));

//获取上周 游戏礼包发放统计
$gameSql = 'SELECT id from rht_apps WHERE ispack = 1';
$gameRs = $train_conn->query($gameSql);
$gameRs->setFetchMode(PDO::FETCH_ASSOC);
$gameRows = $gameRs->fetchAll();
$deleteDlSql = 'DELETE FROM rhc_package_weekly WHERE startday = "'.$startday.'"';
$stmt = $tongji_conn->prepare($deleteDlSql);
$stmt->execute();
$stmt = NULL;
foreach($gameRows as $k=>$v){
   $tempsql = '
   SELECT sum(send_times) as send_times FROM rhc_package_daily WHERE game = '.$v['id'].' AND day BETWEEN "'.$startday.'" AND "'.$endday.'"';
    $tempRs = $tongji_conn->query($tempsql);
    $tempRs->setFetchMode(PDO::FETCH_ASSOC);
    $tempRow = $tempRs->fetch();
    $tempArr['game'] = $v['id'];
    $tempArr['send_times'] = intval($tempRow['send_times']);
    $insertSql = "INSERT INTO rhc_package_weekly(startday,endday,send_times,game) VALUES (?,?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($startday,$endday,$tempArr['send_times'],$tempArr['game']);
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind);
    $stmt = NULL;
}

 ?>
