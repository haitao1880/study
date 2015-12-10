<?php
define('ROOT_PATH', dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR);
define('CONF_PATH', ROOT_PATH.'protected\configs'.DIRECTORY_SEPARATOR);
require_once CONF_PATH.'config.php';
$tongji_dsn = 'mysql:host='.$G_X['rht_static']['host'].';port='.$G_X['rht_static']['port'].';dbname='.$G_X['rht_static']['dbname'];
$tongji_conn = new PDO($tongji_dsn,$G_X['rht_static']['username'],$G_X['rht_static']['password']);
$train_dsn = 'mysql:host='.$G_X['rht_member']['host'].';port='.$G_X['rht_member']['port'].';dbname='.$G_X['rht_member']['dbname'];
$train_conn = new PDO($train_dsn,$G_X['rht_member']['username'],$G_X['rht_member']['password']);
$predate = date('Y-m-01');
$currentdate = date('Y-m-01',strtotime('+1 month'));
$premiketime = strtotime($predate);
$curmiketime = strtotime($currentdate)-1;
$month = date('Y-m');
//获取本月 平台启动次数 平台pv
$viewSql = '
    SELECT pv.total_pv,start_times.total_start_times FROM
	(SELECT count(id) AS total_pv FROM rhc_game_platform WHERE create_time BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 0 ) AS pv,
	(SELECT count(id) AS total_start_times FROM rhc_game_platform WHERE create_time BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 1 ) AS start_times';
$onlineSql = 'SELECT sum(create_time) as c, sum(update_time) as u FROM rhc_useronline WHERE create_time > '.$premiketime.' AND update_time < '.$curmiketime.' GROUP BY client';
$onlineRs = $tongji_conn->query($onlineSql);
$onlineRs->setFetchMode(PDO::FETCH_ASSOC);
$onlineRows = $onlineRs->fetchAll();
$total_time = $per_online = 0;
foreach($onlineRows as $v){
    $tempTime = $v['u'] - $v['c'];
    $total_time += $tempTime;
}
$total_time = round($total_time/3600,2);
$viewRs = $tongji_conn->query($viewSql);
$viewRs->setFetchMode(PDO::FETCH_ASSOC);
$viewRow = $viewRs->fetch();
$viewRow['per_view'] = $viewRow['total_start_times'] ? round($viewRow['total_pv']/$viewRow['total_start_times'],2) : 0;
$per_online = $viewRow['total_start_times'] ? round($total_time/$viewRow['total_start_times'],2) : 0;
$dailyViewSql = 'SELECT id FROM rhc_view_monthly WHERE month = "'.$month.'"';
$dailyViewRs = $tongji_conn->query($dailyViewSql);
$dailyViewRs->setFetchMode(PDO::FETCH_ASSOC);
$dailyViewRow = $dailyViewRs->fetch();
if($dailyViewRow){
    $updateSql = "UPDATE rhc_view_monthly SET start_times = ?,page_views = ?,per_view = ?,online_times = ?,per_online = ? WHERE month = ?";
	$bind = array();
	$bind = array(intval($viewRow['total_start_times']),intval($viewRow['total_pv']),intval($viewRow['per_view']),$total_time,$per_online,$month);
	$stmt = $tongji_conn->prepare($updateSql);
	$stmt->execute($bind);
}else{
    $insertSql = "INSERT INTO rhc_view_monthly(month,start_times,page_views,per_view,online_times,per_online) VALUES (?,?,?,?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($month,intval($viewRow['total_start_times']),intval($viewRow['total_pv']),intval($viewRow['per_view']),$total_time,$per_online);
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind); 
}
$stmt = NULL;

//获取本月 新增用户 活跃用户
$new_user = $active_user = 0;
$userSql = 'SELECT DISTINCT client FROM rhc_game_platform WHERE create_time BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 1';
$userRs = $tongji_conn->query($userSql);
$userRs->setFetchMode(PDO::FETCH_ASSOC);
$userRows = $userRs->fetchAll();
$active_user = count($userRows);
foreach($userRows as $v){
    $tempSql = 'SELECT id FROM rhc_client WHERE client = '.$v['client'].' limit 1';
    $tempRs = $tongji_conn->query($tempSql);
    $tempRs->setFetchMode(PDO::FETCH_ASSOC);
    $tempRow = $tempRs->fetch();
    if(!$tempRow){
        $new_user++;
    }  
}
$old_user = intval($active_user) - intval($new_user);
$dailyMemberSql = 'SELECT id FROM rhc_member_monthly WHERE month = "'.$month.'"';
$dailyMemberRs = $tongji_conn->query($dailyMemberSql);
$dailyMemberRs->setFetchMode(PDO::FETCH_ASSOC);
$dailyMemberRow = $dailyMemberRs->fetch();
if($dailyMemberRow){
    $updateSql = "UPDATE rhc_member_monthly SET new_user = ?,old_user = ?,active_user = ? WHERE month = ?";
	$bind = array();
	$bind = array(intval($new_user),$old_user,intval($active_user),$month);
	$stmt = $tongji_conn->prepare($updateSql);
	$stmt->execute($bind);
}else{
    $insertSql = "INSERT INTO rhc_member_monthly(month,new_user,old_user,active_user) VALUES (?,?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($month,intval($new_user),$old_user,intval($active_user));
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind); 
}
$stmt = NULL;

//获取本月 平台启动次数 签到人数
$signSql = '
    SELECT sign_times.total_sign_times,start_times.total_start_times FROM
	(SELECT count(id) AS total_sign_times FROM rhc_game_platform WHERE create_time BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 402 ) AS sign_times,
	(SELECT count(id) AS total_start_times FROM rhc_game_platform WHERE create_time BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 1 ) AS start_times';

$signRs = $tongji_conn->query($signSql);
$signRs->setFetchMode(PDO::FETCH_ASSOC);
$signRow = $signRs->fetch();
$dailySignSql = 'SELECT id FROM rhc_sign_monthly WHERE month = "'.$month.'"';
$dailySignRs = $tongji_conn->query($dailySignSql);
$dailySignRs->setFetchMode(PDO::FETCH_ASSOC);
$dailySignRow = $dailySignRs->fetch();
if($dailySignRow){
    $updateSql = "UPDATE rhc_sign_monthly SET sign_times = ?,start_times = ? WHERE month = ?";
	$bind = array();
	$bind = array(intval($signRow['total_sign_times']),intval($signRow['total_start_times']),$month);
	$stmt = $tongji_conn->prepare($updateSql);
	$stmt->execute($bind);
}else{
    $insertSql = "INSERT INTO rhc_sign_monthly(month,sign_times,start_times) VALUES (?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($month,intval($signRow['total_sign_times']),intval($signRow['total_start_times']));
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind); 
}
$stmt = NULL;

//获取本月 积分发放统计
$pointSql = '
    SELECT send_points.total_send_points,cost_points.total_cost_points,sign_points.total_sign_points,download_points.total_download_points,buy_points.total_buy_points,package_points.total_package_points,huoban_points.total_huoban_points,huafei_points.total_huafei_points,other_points.total_other_points FROM
	(SELECT sum(points) AS total_send_points FROM rht_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND optype = 1 ) AS send_points,
    (SELECT sum(points) AS total_cost_points FROM rht_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND optype = 0 ) AS cost_points,
    (SELECT sum(points) AS total_buy_points FROM rht_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 0 ) AS buy_points,
    (SELECT sum(points) AS total_sign_points FROM rht_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 1 ) AS sign_points,
    (SELECT sum(points) AS total_download_points FROM rht_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 2 ) AS download_points,
    (SELECT sum(points) AS total_package_points FROM rht_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 3 ) AS package_points,
    (SELECT sum(points) AS total_huoban_points FROM rht_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 4 ) AS huoban_points,
    (SELECT sum(points) AS total_huafei_points FROM rht_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 5 ) AS huafei_points,
    (SELECT sum(points) AS total_other_points FROM rht_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 6 ) AS other_points'
    ;

$pointRs = $train_conn->query($pointSql);
$pointRs->setFetchMode(PDO::FETCH_ASSOC);
$pointRow = $pointRs->fetch();
$dailyPointSql = 'SELECT id FROM rhc_point_monthly WHERE month = "'.$month.'"';
$dailyPointRs = $tongji_conn->query($dailyPointSql);
$dailyPointRs->setFetchMode(PDO::FETCH_ASSOC);
$dailyPointRow = $dailyPointRs->fetch();
if($dailyPointRow){
    $updateSql = "UPDATE rhc_point_monthly SET total_send_point = ?,total_use_point = ?,sign_point = ?,download_point = ?,buy_point = ?,package_point = ?,huoban_point = ?,huafei_point = ?,other_point = ? WHERE month = ?";
	$bind = array();
	$bind = array(intval($pointRow['total_send_points']),intval($pointRow['total_cost_points']),intval($pointRow['total_sign_points']),intval($pointRow['total_download_points']),intval($pointRow['total_buy_points']),intval($pointRow['total_package_points']),intval($pointRow['total_huoban_points']),intval($pointRow['total_huafei_points']),intval($pointRow['total_other_points']),$month);
	$stmt = $tongji_conn->prepare($updateSql);
	$stmt->execute($bind);
}else{
    $insertSql = "INSERT INTO rhc_point_monthly(month,total_send_point,total_use_point,sign_point,download_point,buy_point,package_point,huoban_point,huafei_point,other_point) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($month,intval($pointRow['total_send_points']),intval($pointRow['total_cost_points' ]),intval($pointRow['total_sign_points']),intval($pointRow['total_download_points']),intval($pointRow['total_buy_points']),intval($pointRow['total_package_points']),intval($pointRow['total_huoban_points']),intval($pointRow['total_huafei_points']),intval($pointRow['total_other_points']));
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind); 
}
$stmt = NULL;

//获取本月 按钮点击统计
$clickSql = 'SELECT type,count(id) AS total_times FROM rhc_game_platform WHERE create_time BETWEEN '.$premiketime.' AND '.$curmiketime.' GROUP BY type';
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
$dailyClickSql = 'SELECT id FROM rhc_click_monthly WHERE month = "'.$month.'"';
$dailyClickRs = $tongji_conn->query($dailyClickSql);
$dailyClickRs->setFetchMode(PDO::FETCH_ASSOC);
$dailyClickRow = $dailyClickRs->fetch();
if($dailyClickRow){
    $updateSql = "UPDATE rhc_click_monthly SET btn_201 = ?,btn_202 = ?,btn_203 = ?,btn_204 = ?,btn_205 = ?,btn_207 = ?,btn_208 = ?,btn_301 = ?,btn_302 = ?,btn_303 = ?,btn_401 = ?,btn_402 = ?,btn_501 = ?,btn_601 = ?,btn_602 = ?,btn_603 = ?,btn_604 = ?,btn_605 = ?,btn_701 = ?,btn_702 = ?,btn_801 = ? WHERE month = ?";
	$bind = array();
	$bind = array(intval($clickArr['201']),intval($clickArr['202']),intval($clickArr['203']),intval($clickArr['204']),intval($clickArr['205']),intval($clickArr['207']),intval($clickArr['208']),intval($clickArr['301']),intval($clickArr['302']),intval($clickArr['303']),intval($clickArr['401']),intval($clickArr['402']),intval($clickArr['501']),intval($clickArr['601']),intval($clickArr['602']),intval($clickArr['603']),intval($clickArr['604']),intval($clickArr['605']),intval($clickArr['701']),intval($clickArr['702']),intval($clickArr['801']),$month);
	$stmt = $tongji_conn->prepare($updateSql);
	$stmt->execute($bind);
}else{
    $insertSql = "INSERT INTO rhc_click_monthly(month,btn_201,btn_202,btn_203,btn_204,btn_205,btn_207,btn_208,btn_301,btn_302,btn_303,btn_401,btn_402,btn_501,btn_601,btn_602,btn_603,btn_604,btn_605,btn_701,btn_702,btn_801) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($month,intval($clickArr['201']),intval($clickArr['202']),intval($clickArr['203']),intval($clickArr['204']),intval($clickArr['205']),intval($clickArr['207']),intval($clickArr['208']),intval($clickArr['301']),intval($clickArr['302']),intval($clickArr['303']),intval($clickArr['401']),intval($clickArr['402']),intval($clickArr['501']),intval($clickArr['601']),intval($clickArr['602']),intval($clickArr['603']),intval($clickArr['604']),intval($clickArr['605']),intval($clickArr['701']),intval($clickArr['702']),intval($clickArr['801']));
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind); 
}
$stmt = NULL;

//获取本月 游戏下载统计
$clickDownloadSql = 'SELECT pid,count(id) AS total_download FROM rhc_game_platform WHERE create_time BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 201 GROUP BY pid';
$clickDownloadRs = $tongji_conn->query($clickDownloadSql);
$clickDownloadRs->setFetchMode(PDO::FETCH_ASSOC);
$clickDownloadRows = $clickDownloadRs->fetchAll();
$deleteDlSql = 'DELETE FROM rhc_download_monthly WHERE month = "'.$month.'"';
$stmt = $tongji_conn->prepare($deleteDlSql);
$stmt->execute();
$stmt = NULL;
foreach($clickDownloadRows as $k=>$v){
    $tempsql = '
    SELECT * FROM
    (SELECT count(id) AS total_dl_webinstalled FROM rhc_game_platform WHERE create_time >= '.$premiketime.' AND create_time < '.$curmiketime.' AND type = 202  AND pid = '.$v['pid'].') AS dl_webinstalled,
    (SELECT count(id) AS total_dl_webopened FROM rhc_game_platform WHERE create_time >= '.$premiketime.' AND create_time < '.$curmiketime.' AND type = 203  AND pid = '.$v['pid'].') AS dl_webopened,
	(SELECT count(id) AS total_dl_confirmed FROM rhc_game_platform WHERE create_time >= '.$premiketime.' AND create_time < '.$curmiketime.' AND type = 204  AND pid = '.$v['pid'].') AS dl_confirmed,
    (SELECT count(id) AS total_dl_completed FROM rhc_game_platform WHERE create_time >= '.$premiketime.' AND create_time < '.$curmiketime.' AND type = 206  AND pid = '.$v['pid'].') AS dl_completed,
	(SELECT count(id) AS total_dl_installed FROM rhc_game_platform WHERE create_time >= '.$premiketime.' AND create_time < '.$curmiketime.' AND type = 207  AND pid = '.$v['pid'].') AS dl_installed
    ';
    $tempRs = $tongji_conn->query($tempsql);
    $tempRs->setFetchMode(PDO::FETCH_ASSOC);
    $tempRow = $tempRs->fetch();
    $tempArr['game'] = $v['pid'];
    $tempArr['total_download'] = intval($v['total_download']);
    $tempArr['dl_webinstalled'] = intval($tempRow['total_dl_webinstalled']);
    $tempArr['dl_webopened'] = intval($tempRow['total_dl_webopened']);
    $tempArr['dl_completed'] = intval($tempRow['total_dl_completed']);
    $tempArr['dl_confirmed'] = intval($tempRow['total_dl_confirmed']);
    $tempArr['dl_nocompleted'] = $tempArr['dl_confirmed']-$tempArr['dl_completed'];
    $tempArr['dl_installed'] = intval($tempRow['total_dl_installed']);
    $tempArr['dl_noinstalled'] = $tempArr['dl_confirmed']-$tempArr['dl_installed'];
    $insertSql = "INSERT INTO rhc_download_monthly(month,total_download,dl_completed,dl_nocompleted,dl_noinstalled,dl_installed,dl_confirmed,dl_webinstalled,dl_webopened,game) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($month,$tempArr['total_download'],$tempArr['dl_completed'],$tempArr['dl_nocompleted'],$tempArr['dl_noinstalled'],$tempArr['dl_installed'],$tempArr['dl_confirmed'],$tempArr['dl_webinstalled'],$tempArr['dl_webopened'],$tempArr['game']);
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind);
    $stmt = NULL;
}

//获取本月 游戏礼包发放统计
$packageSql = 'SELECT pid,count(id) AS total_send_times FROM rhc_game_platform WHERE create_time BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 303 GROUP BY pid';
$packageRs = $tongji_conn->query($packageSql);
$packageRs->setFetchMode(PDO::FETCH_ASSOC);
$packageRows = $packageRs->fetchAll();
$deletePkSql = 'DELETE FROM rhc_package_monthly WHERE month = "'.$month.'"';
$stmt = $tongji_conn->prepare($deletePkSql);
$stmt->execute();
$stmt = NULL;
foreach($packageRows as $k=>$v){
    $tempArr['game'] = $v['pid'];
    $tempArr['send_times'] = intval($v['total_send_times']);
    $insertSql = "INSERT INTO rhc_package_monthly(month,send_times,game) VALUES (?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($month,$tempArr['send_times'],$tempArr['game']);
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind);
    $stmt = NULL;
}

 
 
 ?>