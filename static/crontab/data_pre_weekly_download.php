<?php
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'tongji','RN}_IwoAGVYh5xYh]-=-');
$train_dsn = 'mysql:host=localhost;port=3306;dbname=rht_train';
$train_conn = new PDO($train_dsn,'root','password');
$predate = date('Y-m-d');
$tempdate = date('Y-m-d',strtotime("$predate Monday"));
$startday = date('Y-m-d',strtotime("$tempdate -14 days"));
$endday = date('Y-m-d',strtotime("$tempdate -8 days"));

//获取上周 游戏下载统计
$gameSql = 'SELECT id from rht_apps WHERE appcol = 1';
$gameRs = $train_conn->query($gameSql);
$gameRs->setFetchMode(PDO::FETCH_ASSOC);
$gameRows = $gameRs->fetchAll();
$deleteDlSql = 'DELETE FROM rhc_download_weekly WHERE startday = "'.$startday.'"';
$stmt = $tongji_conn->prepare($deleteDlSql);
$stmt->execute();
$stmt = NULL;
foreach($gameRows as $k=>$v){
    $tempsql = '
   SELECT sum(total_download) as total_download,sum(dl_completed) as dl_completed,sum(dl_nocompleted) as dl_nocompleted,sum(dl_noinstalled) as dl_noinstalled,sum(dl_installed) as dl_installed,sum(dl_confirmed) as dl_confirmed,sum(dl_webinstalled) as dl_webinstalled,sum(dl_webopened) as dl_webopened FROM rhc_download_daily WHERE game = '.$v['id'].' AND day BETWEEN "'.$startday.'" AND "'.$endday.'"';
    $tempRs = $tongji_conn->query($tempsql);
    $tempRs->setFetchMode(PDO::FETCH_ASSOC);
    $tempRow = $tempRs->fetch();
    $tempArr['game'] = $v['id'];
    $tempArr['total_download'] = intval($tempRow['total_download']);
    $tempArr['dl_webinstalled'] = intval($tempRow['dl_webinstalled']);
    $tempArr['dl_webopened'] = intval($tempRow['dl_webopened']);
    $tempArr['dl_completed'] = intval($tempRow['dl_completed']);
    $tempArr['dl_confirmed'] = intval($tempRow['dl_confirmed']);
    $tempArr['dl_nocompleted'] = $tempArr['dl_confirmed']-$tempArr['dl_completed'];
    $tempArr['dl_installed'] = intval($tempRow['dl_installed']);
    $tempArr['dl_noinstalled'] = $tempArr['dl_confirmed']-$tempArr['dl_installed'];
    $insertSql = "INSERT INTO rhc_download_weekly(startday,endday,total_download,dl_completed,dl_nocompleted,dl_noinstalled,dl_installed,dl_confirmed,dl_webinstalled,dl_webopened,game) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($startday,$endday,$tempArr['total_download'],$tempArr['dl_completed'],$tempArr['dl_nocompleted'],$tempArr['dl_noinstalled'],$tempArr['dl_installed'],$tempArr['dl_confirmed'],$tempArr['dl_webinstalled'],$tempArr['dl_webopened'],$tempArr['game']);
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind);
    $stmt = NULL;
}

 ?>
