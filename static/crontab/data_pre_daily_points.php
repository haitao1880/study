<?php
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'tongji','RN}_IwoAGVYh5xYh]-=-');
$train_dsn = 'mysql:host=localhost;port=3306;dbname=rht_idc';
$train_conn = new PDO($train_dsn,'root','password');
$predate = date('Y-m-d',strtotime('-1 days'));
$tempPredate = date('Ymd',strtotime('-1 days'));
$currentdate = date('Y-m-d');
$premiketime = strtotime($predate);
$curmiketime = strtotime($currentdate)-1;

//获取 积分发放统计
$pointSql = '
    SELECT send_points.total_send_points,cost_points.total_cost_points,sign_points.total_sign_points,download_points.total_download_points,buy_points.total_buy_points,package_points.total_package_points,huoban_points.total_huoban_points,huafei_points.total_huafei_points,other_points.total_other_points FROM
	(SELECT sum(points) AS total_send_points FROM rhi_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND optype = 1 ) AS send_points,
    (SELECT sum(points) AS total_cost_points FROM rhi_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND optype = 0 ) AS cost_points,
    (SELECT sum(points) AS total_buy_points FROM rhi_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 0 ) AS buy_points,
    (SELECT sum(points) AS total_sign_points FROM rhi_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 1 ) AS sign_points,
    (SELECT sum(points) AS total_download_points FROM rhi_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 2 ) AS download_points,
    (SELECT sum(points) AS total_package_points FROM rhi_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 3 ) AS package_points,
    (SELECT sum(points) AS total_huoban_points FROM rhi_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 4 ) AS huoban_points,
    (SELECT sum(points) AS total_huafei_points FROM rhi_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 5 ) AS huafei_points,
    (SELECT sum(points) AS total_other_points FROM rhi_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 6 ) AS other_points'
    ;
$pointRs = $train_conn->query($pointSql);
$pointRs->setFetchMode(PDO::FETCH_ASSOC);
$pointRow = $pointRs->fetch();

$updateSql = "UPDATE rhc_point_daily SET total_send_point = ?,total_use_point = ?,sign_point = ?,download_point = ?,buy_point = ?,package_point = ?,huoban_point = ?,huafei_point = ?,other_point = ? WHERE day = ?";
$bind = array();
$bind = array(intval($pointRow['total_send_points']),intval($pointRow['total_cost_points']),intval($pointRow['total_sign_points']),intval($pointRow['total_download_points']),intval($pointRow['total_buy_points']),intval($pointRow['total_package_points']),intval($pointRow['total_huoban_points']),intval($pointRow['total_huafei_points']),intval($pointRow['total_other_points']),$predate);
$stmt = $tongji_conn->prepare($updateSql);
$stmt->execute($bind);
$stmt = NULL;
 ?>
