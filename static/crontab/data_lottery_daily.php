<?php
date_default_timezone_set('PRC');
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'root','password');
$idc_dsn = 'mysql:host=localhost;port=3306;dbname=rht_idc';
$idc_conn = new PDO($idc_dsn,'root','password');
$account_dsn = 'mysql:host=localhost;port=3306;dbname=rht_idc';
$account_conn = new PDO($account_dsn,'root','password');
$predate = date('Y-m-d',strtotime('-1 days'));
$tempPredate = date('Ymd',strtotime('-1 days'));
$currentdate = date('Y-m-d');
$premiketime = strtotime($predate);
$curmiketime = strtotime($currentdate)-1;

$new_reg = $new_num = $start_num = $view_times = $total_first = $total_second = $total_third = $total_fourth = $total_fifth = $total_first_point = $total_second_point = $total_third_point = $total_fourth_point = $total_fifth_point = $total_times = $total_point = $total_cost_point = $total_sharetimes = $total_shareok = $dl_click = $dj_click = $dl_andclick = $dl_iosclick = $sure_click = $cancel_click = $share_open_user = 0;

$shareSql = '
    SELECT sharetimes.total_sharetimes,shareok.total_shareok,viewtimes.total_views,dltimes.total_dltimes,djtimes.total_djtimes,andltimes.total_andltimes,isodltimes.total_isodltimes,suretimes.total_suretimes,canceltimes.total_canceltimes FROM
	(SELECT count(id) AS total_sharetimes FROM rhc_game_platform WHERE cday = '.$tempPredate.' AND type = 804) AS sharetimes,
    (SELECT count(id) AS total_shareok FROM rhc_game_platform WHERE cday = '.$tempPredate.' AND type = 805) AS shareok,
    (SELECT count(id) AS total_views FROM rhc_game_platform WHERE cday = '.$tempPredate.' AND type = 802) AS viewtimes,
    (SELECT count(id) AS total_dltimes FROM rhc_game_platform WHERE cday = '.$tempPredate.' AND type = 808) AS dltimes,
    (SELECT count(id) AS total_djtimes FROM rhc_game_platform WHERE cday = '.$tempPredate.' AND type = 809) AS djtimes,
    (SELECT count(id) AS total_andltimes FROM rhc_game_platform WHERE cday = '.$tempPredate.' AND type = 810) AS andltimes,
    (SELECT count(id) AS total_isodltimes FROM rhc_game_platform WHERE cday = '.$tempPredate.' AND type = 813) AS isodltimes,
    (SELECT count(id) AS total_suretimes FROM rhc_game_platform WHERE cday = '.$tempPredate.' AND type = 811) AS suretimes,
    (SELECT count(id) AS total_canceltimes FROM rhc_game_platform WHERE cday = '.$tempPredate.' AND type = 812) AS canceltimes';

$rs = $tongji_conn->query($shareSql);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row = $rs->fetch();
$total_sharetimes = intval($row['total_sharetimes']);
$total_shareok = intval($row['total_shareok']);
$view_times = intval($row['total_views']);
$dl_click = intval($row['total_dltimes']);
$dj_click = intval($row['total_djtimes']);
$dl_andclick = intval($row['total_andltimes']);
$dl_iosclick = intval($row['total_isodltimes']);
$sure_click = intval($row['total_suretimes']);
$cancel_click = intval($row['total_canceltimes']);

//获取前一天 新开启用户 注册用户
$new_num = 0;
$userSql = 'SELECT DISTINCT client FROM rhc_game_platform WHERE cday = "'.$tempPredate.'" AND type = 802';
$userRs = $tongji_conn->query($userSql);
$userRs->setFetchMode(PDO::FETCH_ASSOC);
$userRows = $userRs->fetchAll();
foreach($userRows as $v){
    $tempSql = 'SELECT id FROM rhc_hdclient WHERE client = "'.strtoupper($v['client']).'" AND type = 1 limit 1';
    $tempRs = $tongji_conn->query($tempSql);
    $tempRs->setFetchMode(PDO::FETCH_ASSOC);
    $tempRow = $tempRs->fetch();
    if(!$tempRow){
        $new_num++;
    }  
}

$regsql = 'SELECT count(id) as total_num FROM rhi_account WHERE trainno = "HD" AND regtime between '.$premiketime.' AND '.$curmiketime;
$regRs = $account_conn->query($regsql);
$regRs->setFetchMode(PDO::FETCH_ASSOC);
$regRow = $regRs->fetch();
$new_reg = intval($regRow['total_num']);

$sql = '
    SELECT start.start_num,first.total_first,second.total_second,third.total_third,fourth.total_fourth,fifth.total_fifth,first_point.total_first_point,second_point.total_second_point,third_point.total_third_point,fourth_point.total_fourth_point,fifth_point.total_fifth_point FROM
	(SELECT count(id) AS start_num FROM rhi_lottery WHERE createtime BETWEEN '.$premiketime.' AND '.$curmiketime.') AS start,
    (SELECT count(id) AS total_first FROM rhi_lottery WHERE freetouse BETWEEN '.$premiketime.' AND '.$curmiketime.') AS first,
    (SELECT count(id) AS total_second FROM rhi_lottery WHERE score1touse BETWEEN '.$premiketime.' AND '.$curmiketime.') AS second,
    (SELECT count(id) AS total_third FROM rhi_lottery WHERE score2touse BETWEEN '.$premiketime.' AND '.$curmiketime.') AS third,
    (SELECT count(id) AS total_fourth FROM rhi_lottery WHERE score3touse BETWEEN '.$premiketime.' AND '.$curmiketime.') AS fourth,
    (SELECT count(id) AS total_fifth FROM rhi_lottery WHERE sharetouse BETWEEN '.$premiketime.' AND '.$curmiketime.') AS fifth,
    (SELECT sum(freetopoint) AS total_first_point FROM rhi_lottery WHERE freetouse BETWEEN '.$premiketime.' AND '.$curmiketime.') AS first_point,
    (SELECT sum(score1topoint) AS total_second_point FROM rhi_lottery WHERE freetouse BETWEEN '.$premiketime.' AND '.$curmiketime.') AS second_point,
    (SELECT sum(score2topoint) AS total_third_point FROM rhi_lottery WHERE freetouse BETWEEN '.$premiketime.' AND '.$curmiketime.') AS third_point,
    (SELECT sum(score3topoint) AS total_fourth_point FROM rhi_lottery WHERE freetouse BETWEEN '.$premiketime.' AND '.$curmiketime.') AS fourth_point,
    (SELECT sum(sharetopoint) AS total_fifth_point FROM rhi_lottery WHERE freetouse BETWEEN '.$premiketime.' AND '.$curmiketime.') AS fifth_point';

$rs = $idc_conn->query($sql);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row = $rs->fetch();
$start_num = intval($row['start_num']);
$total_first = intval($row['total_first']);
$total_second = intval($row['total_second']);
$total_third = intval($row['total_third']);
$total_fourth = intval($row['total_fourth']);
$total_fifth = intval($row['total_fifth']);
$total_first_point = intval($row['total_first_point']);
$total_second_point = intval($row['total_second_point']);
$total_third_point = intval($row['total_third_point']);
$total_fourth_point = intval($row['total_fourth_point']);
$total_fifth_point = intval($row['total_fifth_point']);
$total_times = $total_first+$total_second+$total_third+$total_fourth+$total_fifth;
$total_point = $total_first_point+$total_second_point+$total_third_point+$total_fourth_point+$total_fifth_point;

$pointSql = 'SELECT sum(points) as total_cost FROM rhi_points WHERE ctime BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 7 AND optype = 0';
$rs = $idc_conn->query($pointSql);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row = $rs->fetch();
$total_cost_point = abs(intval($row['total_cost']));


$dailySql = 'SELECT id FROM rhc_wxlottery_daily WHERE day = "'.$predate.'"';
$dailyRs = $tongji_conn->query($dailySql);
$dailyRs->setFetchMode(PDO::FETCH_ASSOC);
$dailyRow = $dailyRs->fetch();
if($dailyRow){
    $updateSql = "UPDATE rhc_wxlottery_daily SET view_times = ?,start_num = ?,do_times = ?,first_join = ?,second_join = ?,third_join = ?,fourth_join = ?,fifth_join = ?,cost_points = ?,get_points = ?,first_get_points = ?,second_get_points = ?,third_get_points = ?,fourth_get_points = ?,fifth_get_points = ?,sharetimes = ?,shareok = ?,new_reg = ?,new_num = ?,dl_click = ?,dj_click = ?,dl_andclick = ?,dl_iosclick = ?,sure_click = ?,cancel_click = ?,share_open_user = ? WHERE day = ?";
	$bind = array();
	$bind = array($view_times,$start_num,$total_times,$total_first,$total_second,$total_third,$total_fourth,$total_fifth,$total_cost_point,$total_point,$total_first_point,$total_second_point,$total_third_point,$total_fourth_point,$total_fifth_point,$total_sharetimes,$total_shareok,$new_reg,$new_num,$dl_click,$dj_click,$dl_andclick,$dl_iosclick,$sure_click,$cancel_click,$share_open_user,$predate);
	$stmt = $tongji_conn->prepare($updateSql);
	$stmt->execute($bind);
}else{
    
    $insertSql = "INSERT INTO rhc_wxlottery_daily(view_times,start_num,do_times,first_join,second_join,third_join,fourth_join,fifth_join,cost_points,get_points,first_get_points,second_get_points,third_get_points,fourth_get_points,fifth_get_points,sharetimes,shareok,new_reg,new_num,dl_click,dj_click,dl_andclick,dl_iosclick,sure_click,cancel_click,share_open_user,day) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($view_times,$start_num,$total_times,$total_first,$total_second,$total_third,$total_fourth,$total_fifth,$total_cost_point,$total_point,$total_first_point,$total_second_point,$total_third_point,$total_fourth_point,$total_fifth_point,$total_sharetimes,$total_shareok,$new_reg,$new_num,$dl_click,$dj_click,$dl_andclick,$dl_iosclick,$sure_click,$cancel_click,$share_open_user,$predate);
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind); 
}
$stmt = NULL;


 ?>
