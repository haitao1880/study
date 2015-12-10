<?php
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'tongji','RN}_IwoAGVYh5xYh]-=-');
$predate = date('Y-m-d');
$tempPredate = date('Ymd');
$currentdate = date('Y-m-d',strtotime('+1 days'));
$premiketime = strtotime($predate);
$curmiketime = strtotime($currentdate)-1;

//获取 按钮点击统计
$clickSql = 'SELECT type,count(id) AS total_times FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" GROUP BY type';
$clickRs = $tongji_conn->query($clickSql);
$clickRs->setFetchMode(PDO::FETCH_ASSOC);
$clickRow = $clickRs->fetchAll();
$btnArr = array('201','202','203','204','205','207','208','301','302','303','401','402','501','601','602','603','604','605','701','702','801');
$clickArr = array();
foreach($btnArr as $vv){
    $clickArr[$vv] = 0;
    foreach($clickRow as $k=>$v){
        if($vv = $v['type']){
            $clickArr[$vv] = $v['total_times'];
        }
    }
}
$dailyClickSql = 'SELECT id FROM rhc_click_daily WHERE day = "'.$predate.'"';
$dailyClickRs = $tongji_conn->query($dailyClickSql);
$dailyClickRs->setFetchMode(PDO::FETCH_ASSOC);
$dailyClickRow = $dailyClickRs->fetch();
if($dailyClickRow){
    $updateSql = "UPDATE rhc_click_daily SET btn_201 = ?,btn_202 = ?,btn_203 = ?,btn_204 = ?,btn_205 = ?,btn_207 = ?,btn_208 = ?,btn_301 = ?,btn_302 = ?,btn_303 = ?,btn_401 = ?,btn_402 = ?,btn_501 = ?,btn_601 = ?,btn_602 = ?,btn_603 = ?,btn_604 = ?,btn_605 = ?,btn_701 = ?,btn_702 = ?,btn_801 = ? WHERE day = ?";
	$bind = array();
	$bind = array(intval($clickArr['201']),intval($clickArr['202']),intval($clickArr['203']),intval($clickArr['204']),intval($clickArr['205']),intval($clickArr['207']),intval($clickArr['208']),intval($clickArr['301']),intval($clickArr['302']),intval($clickArr['303']),intval($clickArr['401']),intval($clickArr['402']),intval($clickArr['501']),intval($clickArr['601']),intval($clickArr['602']),intval($clickArr['603']),intval($clickArr['604']),intval($clickArr['605']),intval($clickArr['701']),intval($clickArr['702']),intval($clickArr['801']),$predate);
	$stmt = $tongji_conn->prepare($updateSql);
	$stmt->execute($bind);
}else{
    $insertSql = "INSERT INTO rhc_click_daily(day,btn_201,btn_202,btn_203,btn_204,btn_205,btn_207,btn_208,btn_301,btn_302,btn_303,btn_401,btn_402,btn_501,btn_601,btn_602,btn_603,btn_604,btn_605,btn_701,btn_702,btn_801) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($predate,intval($clickArr['201']),intval($clickArr['202']),intval($clickArr['203']),intval($clickArr['204']),intval($clickArr['205']),intval($clickArr['207']),intval($clickArr['208']),intval($clickArr['301']),intval($clickArr['302']),intval($clickArr['303']),intval($clickArr['401']),intval($clickArr['402']),intval($clickArr['501']),intval($clickArr['601']),intval($clickArr['602']),intval($clickArr['603']),intval($clickArr['604']),intval($clickArr['605']),intval($clickArr['701']),intval($clickArr['702']),intval($clickArr['801']));
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind); 
}
$stmt = NULL;
 ?>
