<?php
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'tongji','RN}_IwoAGVYh5xYh]-=-');
$train_dsn = 'mysql:host=localhost;port=3306;dbname=rht_train';
$train_conn = new PDO($train_dsn,'root','password');
$predate = date('Y-m-d');
$tempPredate = date('Ymd');
$currentdate = date('Y-m-d',strtotime('+1 days'));
$premiketime = strtotime($predate);
$curmiketime = strtotime($currentdate)-1;

//获取 游戏下载统计
$gameSql = 'SELECT id from rht_apps WHERE appcol = 1';
$gameRs = $train_conn->query($gameSql);
$gameRs->setFetchMode(PDO::FETCH_ASSOC);
$gameRows = $gameRs->fetchAll();
$deleteDlSql = 'DELETE FROM rhc_download_daily WHERE day = "'.$predate.'"';
$stmt = $tongji_conn->prepare($deleteDlSql);
$stmt->execute();
$stmt = NULL;
foreach($gameRows as $k=>$v){
    $tempsql = '
    SELECT * FROM
    (SELECT count(id) AS total_dl_nums FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 201  AND pid = '.$v['id'].') AS dl_nums,
    (SELECT count(id) AS total_dl_webinstalled FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 202  AND pid = '.$v['id'].') AS dl_webinstalled,
    (SELECT count(id) AS total_dl_webopened FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 203  AND pid = '.$v['id'].') AS dl_webopened,
	(SELECT count(id) AS total_dl_confirmed FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 204  AND pid = '.$v['id'].') AS dl_confirmed,
    (SELECT count(id) AS total_dl_completed FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 206  AND pid = '.$v['id'].') AS dl_completed,
	(SELECT count(id) AS total_dl_installed FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 207  AND pid = '.$v['id'].') AS dl_installed
    ';
    $tempRs = $tongji_conn->query($tempsql);
    $tempRs->setFetchMode(PDO::FETCH_ASSOC);
    $tempRow = $tempRs->fetch();
    $tempArr['game'] = $v['id'];
    $tempArr['total_download'] = intval($tempRow['total_dl_nums']);
    $tempArr['dl_webinstalled'] = intval($tempRow['total_dl_webinstalled']);
    $tempArr['dl_webopened'] = intval($tempRow['total_dl_webopened']);
    $tempArr['dl_completed'] = intval($tempRow['total_dl_completed']);
    $tempArr['dl_confirmed'] = intval($tempRow['total_dl_confirmed']);
    $tempArr['dl_nocompleted'] = $tempArr['dl_confirmed']-$tempArr['dl_completed'];
    $tempArr['dl_installed'] = intval($tempRow['total_dl_installed']);
    $tempArr['dl_noinstalled'] = $tempArr['dl_confirmed']-$tempArr['dl_installed'];
    $insertSql = "INSERT INTO rhc_download_daily(day,total_download,dl_completed,dl_nocompleted,dl_noinstalled,dl_installed,dl_confirmed,dl_webinstalled,dl_webopened,game) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($predate,$tempArr['total_download'],$tempArr['dl_completed'],$tempArr['dl_nocompleted'],$tempArr['dl_noinstalled'],$tempArr['dl_installed'],$tempArr['dl_confirmed'],$tempArr['dl_webinstalled'],$tempArr['dl_webopened'],$tempArr['game']);
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind);
    $stmt = NULL;
}
 
 ?>
