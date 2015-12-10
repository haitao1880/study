<?php
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'tongji','RN}_IwoAGVYh5xYh]-=-');
$mac_dsn = 'mysql:host=localhost;port=3306;dbname=rht_admin';
$mac_conn = new PDO($mac_dsn,'root','password');
$predate = date('Y-m-d');
$tempPredate = date('Ymd');
$currentdate = date('Y-m-d',strtotime('+1 days'));
$premiketime = strtotime($predate);
$curmiketime = strtotime($currentdate)-1;
//获取 平台启动次数 平台pv
$viewSql = '
    SELECT pv.total_pv,start_times.total_start_times,startapp_times.total_startapp_times FROM
	(SELECT count(id) AS total_pv FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 0  AND pid = 0 ) AS pv,
	(SELECT count(id) AS total_start_times FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 1  pid in (1,2)) AS start_times,
    (SELECT count(id) AS total_startapp_times FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 1 pid = 0 ) AS startapp_times';

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
$dailyViewSql = 'SELECT id FROM rhc_view_daily WHERE day = "'.$predate.'"';
$dailyViewRs = $tongji_conn->query($dailyViewSql);
$dailyViewRs->setFetchMode(PDO::FETCH_ASSOC);
$dailyViewRow = $dailyViewRs->fetch();
if($dailyViewRow){
    $updateSql = "UPDATE rhc_view_daily SET start_times = ?,page_views = ?,per_view = ?,online_times = ?,per_online = ? WHERE day = ?";
	$bind = array();
	$bind = array(intval($viewRow['total_start_times']),intval($viewRow['total_pv']),$viewRow['per_view'],$total_time,$per_online,$predate);
	$stmt = $tongji_conn->prepare($updateSql);
	$stmt->execute($bind);
}else{
    $insertSql = "INSERT INTO rhc_view_daily(day,start_times,page_views,per_view,online_times,per_online) VALUES (?,?,?,?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($predate,intval($viewRow['total_start_times']),intval($viewRow['total_pv']),intval($viewRow['per_view']),$total_time,$per_online);
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind); 
}
$stmt = NULL;

//获取前一天 新开启游戏平台用户 开启游戏平台用户
$new_user = $active_user = 0;
$userSql = 'SELECT DISTINCT client FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 1 AND pid in (1,2)';
$userRs = $tongji_conn->query($userSql);
$userRs->setFetchMode(PDO::FETCH_ASSOC);
$userRows = $userRs->fetchAll();
$active_user = count($userRows);

foreach($userRows as $v){
    $tempSql = 'SELECT id FROM rha_open_trainapp WHERE usermac = "'.strtoupper($v['client']).'" OR usermac = "'.$v['client'].'" limit 1';
    $tempRs = $mac_conn->query($tempSql);
    $tempRs->setFetchMode(PDO::FETCH_ASSOC);
    $tempRow = $tempRs->fetch();
    if(!$tempRow){
        $new_user++;
    }  
}

$new_appuser = $active_appuser = 0;
$appuserSql = 'SELECT DISTINCT client FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 1 AND pid = 0';
$appuserRs = $tongji_conn->query($appuserSql);
$appuserRs->setFetchMode(PDO::FETCH_ASSOC);
$appuserRows = $appuserRs->fetchAll();
$active_appuser = count($appuserRows);

foreach($appuserRows as $v){
    $tempSql = 'SELECT id FROM rha_open_trainapp WHERE usermac = "'.strtoupper($v['client']).'" OR usermac = "'.$v['client'].'" limit 1';
    $tempRs = $mac_conn->query($tempSql);
    $tempRs->setFetchMode(PDO::FETCH_ASSOC);
    $tempRow = $tempRs->fetch();
    if(!$tempRow){
        $new_appuser++;
    }  
}


$old_user = intval($active_user) - intval($new_user);
$dailyMemberSql = 'SELECT id FROM rhc_member_daily WHERE day = "'.$predate.'"';
$dailyMemberRs = $tongji_conn->query($dailyMemberSql);
$dailyMemberRs->setFetchMode(PDO::FETCH_ASSOC);
$dailyMemberRow = $dailyMemberRs->fetch();
if($dailyMemberRow){
    $updateSql = "UPDATE rhc_member_daily SET new_user = ?,old_user = ?,active_user = ?,open_num = ?,open_app = ?,new_appuser = ?,active_appuser = ? WHERE day = ?";
	$bind = array();
	$bind = array(intval($new_user),$old_user,intval($active_user),intval($viewRow['total_start_times']),intval($viewRow['total_startapp_times']),$new_appuser,$active_appuser,$predate);
	$stmt = $tongji_conn->prepare($updateSql);
	$stmt->execute($bind);
}else{
    $insertSql = "INSERT INTO rhc_member_daily(day,new_user,old_user,active_user,open_num,open_app,new_appuser,active_appuser) VALUES (?,?,?,?,?,?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($predate,intval($new_user),$old_user,intval($active_user),intval($viewRow['total_start_times']),intval($viewRow['total_startapp_times']),$new_appuser,$active_appuser);
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind); 
}
$stmt = NULL;

//获取前一天 平台启动次数 签到人数
$signSql = '
    SELECT sign_times.total_sign_times,start_times.total_start_times FROM
	(SELECT count(id) AS total_sign_times FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 402 ) AS sign_times,
	(SELECT count(id) AS total_start_times FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 401 ) AS start_times';
$signRs = $tongji_conn->query($signSql);
$signRs->setFetchMode(PDO::FETCH_ASSOC);
$signRow = $signRs->fetch();
$dailySignSql = 'SELECT id FROM rhc_sign_daily WHERE day = "'.$predate.'"';
$dailySignRs = $tongji_conn->query($dailySignSql);
$dailySignRs->setFetchMode(PDO::FETCH_ASSOC);
$dailySignRow = $dailySignRs->fetch();
if($dailySignRow){
    $updateSql = "UPDATE rhc_sign_daily SET sign_times = ?,start_times = ? WHERE day = ?";
	$bind = array();
	$bind = array(intval($signRow['total_sign_times']),intval($signRow['total_start_times']),$predate);
	$stmt = $tongji_conn->prepare($updateSql);
	$stmt->execute($bind);
}else{
    $insertSql = "INSERT INTO rhc_sign_daily(day,sign_times,start_times) VALUES (?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($predate,intval($signRow['total_sign_times']),intval($signRow['total_start_times']));
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind); 
}
$stmt = NULL;
 ?>
