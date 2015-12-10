<?php
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'tongji','RN}_IwoAGVYh5xYh]-=-');
$predate = date('Y-m-d',strtotime('-1 days'));
$tempPredate = date('Ymd',strtotime('-1 days'));
$currentdate = date('Y-m-d');
$premiketime = strtotime($predate);
$curmiketime = strtotime($currentdate)-1;
//获取 平台启动次数 平台pv
$viewSql = '
    SELECT pv.total_pv,start_times.total_start_times FROM
	(SELECT count(id) AS total_pv FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 0 ) AS pv,
	(SELECT count(id) AS total_start_times FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 1 ) AS start_times';

$onlineSql = 'SELECT sum(update_time-create_time)/3600 as total_time FROM rhc_useronline WHERE cday = "'.$tempPredate.'"';
$onlineRs = $tongji_conn->query($onlineSql);
$onlineRs->setFetchMode(PDO::FETCH_ASSOC);
$onlineRow = $onlineRs->fetch();
$total_time = $per_online = 0;
$total_time = round($onlineRow['total_time'],2);
$viewRs = $tongji_conn->query($viewSql);
$viewRs->setFetchMode(PDO::FETCH_ASSOC);
$viewRow = $viewRs->fetch();
$viewRow['per_view'] = $viewRow['total_start_times'] ? round(intval($viewRow['total_pv'])/$viewRow['total_start_times'],2) : 0;
$per_online = $viewRow['total_start_times'] ? round($total_time/$viewRow['total_start_times'],2) : 0;

$updateSql = "UPDATE rhc_view_daily SET start_times = ?,page_views = ?,per_view = ?,online_times = ?,per_online = ? WHERE day = ?";
$bind = array();
$bind = array(intval($viewRow['total_start_times']),intval($viewRow['total_pv']),$viewRow['per_view'],$total_time,$per_online,$predate);
$stmt = $tongji_conn->prepare($updateSql);
$stmt->execute($bind);
$stmt = NULL;
 ?>
