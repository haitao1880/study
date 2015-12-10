<?php
date_default_timezone_set('PRC');
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'tongji','RN}_IwoAGVYh5xYh]-=-');
$predate = date('Y-m-d');
$tempdate = date('Y-m-d',strtotime("$predate Monday"));
$startday = date('Y-m-d',strtotime("$tempdate -14 days"));
$endday = date('Y-m-d',strtotime("$tempdate -8 days"));

$startday_01 = date('Y-m-d',strtotime("$tempdate -14 days"));
$endday_01 = date('Y-m-d',strtotime("$tempdate -7 days"));
$premiketime = strtotime($startday_01);
$curmiketime = strtotime($endday_01)-1;

//获取上周 平台启动次数 平台pv
$viewSql = 'SELECT sum(page_views) as total_pv,sum(start_times) as total_start_times,sum(online_times) as total_online_times FROM rhc_view_daily WHERE day BETWEEN "'.$startday.'" AND "'.$endday.'"';
$viewRs = $tongji_conn->query($viewSql);
$viewRs->setFetchMode(PDO::FETCH_ASSOC);
$viewRow = $viewRs->fetch();
$viewRow['per_view'] = $viewRow['total_start_times'] ? round(intval($viewRow['total_pv'])/$viewRow['total_start_times'],2) : 0;
$viewRow['per_online'] = $viewRow['total_start_times'] ? round(intval($viewRow['total_online_times'])/$viewRow['total_start_times'],2) : 0;

$updateSql = "UPDATE rhc_view_weekly SET start_times = ?,page_views = ?,per_view = ?,online_times = ?,per_online = ? WHERE startday = ? AND endday = ?";
$bind = array();
$bind = array(intval($viewRow['total_start_times']),intval($viewRow['total_pv']),$viewRow['per_view'],intval($viewRow['total_online_times']),$viewRow['per_online'],$startday,$endday);
$stmt = $tongji_conn->prepare($updateSql);
$stmt->execute($bind);
$stmt = NULL;

//获取上周 新开启平台用户 开启平台用户
$userSql = 'SELECT sum(new_user) as total_new_user,sum(open_num) as total_open_num FROM rhc_member_daily WHERE day BETWEEN "'.$startday.'" AND "'.$endday.'"';
$userRs = $tongji_conn->query($userSql);
$userRs->setFetchMode(PDO::FETCH_ASSOC);
$userRow = $userRs->fetch();

$userTempSql = 'SELECT COUNT(DISTINCT client) as total_active_user FROM rhc_game_platform WHERE create_time BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 1';
$userTempRs = $tongji_conn->query($userTempSql);
$userTempRs->setFetchMode(PDO::FETCH_ASSOC);
$userTempRow = $userTempRs->fetch();
$old_user = intval($userTempRow['total_active_user'])-intval($userRow['total_new_user']);

$updateSql = "UPDATE rhc_member_weekly SET new_user = ?,old_user = ?,active_user = ?,open_num = ? WHERE startday = ? AND endday = ?";
$bind = array();
$bind = array(intval($userRow['total_new_user']),$old_user,intval($userTempRow['total_active_user']),intval($userRow['total_open_num']),$startday,$endday);
$stmt = $tongji_conn->prepare($updateSql);
$stmt->execute($bind);
$stmt = NULL;

//获取上周 平台启动次数 签到人数
$signSql = 'SELECT sum(sign_times) as total_sign_times,sum(start_times) as total_start_times FROM rhc_sign_daily WHERE day BETWEEN "'.$startday.'" AND "'.$endday.'"';
$signRs = $tongji_conn->query($signSql);
$signRs->setFetchMode(PDO::FETCH_ASSOC);
$signRow = $signRs->fetch();

$updateSql = "UPDATE rhc_sign_weekly SET sign_times = ?,start_times = ? WHERE startday = ? AND endday = ?";
$bind = array();
$bind = array(intval($signRow['total_sign_times']),intval($signRow['total_start_times']),$startday,$endday);
$stmt = $tongji_conn->prepare($updateSql);
$stmt->execute($bind);
$stmt = NULL;

//获取上周 积分发放统计
$pointSql = '
   SELECT sum(total_send_point) as total_send_point,sum(total_use_point) as total_use_point,sum(sign_point) as sign_point,sum(download_point) as download_point,sum(buy_point) as buy_point,sum(package_point) as package_point,sum(huoban_point) as huoban_point,sum(huafei_point) as huafei_point,sum(other_point) as other_point FROM rhc_point_daily WHERE day BETWEEN "'.$startday.'" AND "'.$endday.'"';

$pointRs = $tongji_conn->query($pointSql);
$pointRs->setFetchMode(PDO::FETCH_ASSOC);
$pointRow = $pointRs->fetch();

$updateSql = "UPDATE rhc_point_weekly SET total_send_point = ?,total_use_point = ?,sign_point = ?,download_point = ?,buy_point = ?,package_point = ?,huoban_point = ?,huafei_point = ?,other_point = ? WHERE startday = ? AND endday = ?";
$bind = array();
$bind = array(intval($pointRow['total_send_point']),intval($pointRow['total_use_point']),intval($pointRow['sign_point']),intval($pointRow['download_point']),intval($pointRow['buy_point']),intval($pointRow['package_point']),intval($pointRow['huoban_point']),intval($pointRow['huafei_point']),intval($pointRow['other_point']),$startday,$endday);
$stmt = $tongji_conn->prepare($updateSql);
$stmt->execute($bind);
$stmt = NULL;

//获取上周 按钮点击统计
$clickSql = '
   SELECT sum(btn_201) as btn_201,sum(btn_202) as btn_202,sum(btn_203) as btn_203,sum(btn_204) as btn_204,sum(btn_205) as btn_205,sum(btn_207) as btn_207,sum(btn_208) as btn_208,sum(btn_301) as btn_301,sum(btn_302) as btn_302,sum(btn_303) as btn_303,sum(btn_401) as btn_401,sum(btn_402) as btn_402,sum(btn_501) as btn_501,sum(btn_601) as btn_601,sum(btn_602) as btn_602,sum(btn_603) as btn_603,sum(btn_604) as btn_604,sum(btn_605) as btn_605,sum(btn_701) as btn_701,sum(btn_702) as btn_702,sum(btn_801) as btn_801 FROM rhc_click_daily WHERE day BETWEEN "'.$startday.'" AND "'.$endday.'"';
$clickRs = $tongji_conn->query($clickSql);
$clickRs->setFetchMode(PDO::FETCH_ASSOC);
$clickRow = $clickRs->fetch();

$updateSql = "UPDATE rhc_click_weekly SET btn_201 = ?,btn_202 = ?,btn_203 = ?,btn_204 = ?,btn_205 = ?,btn_207 = ?,btn_208 = ?,btn_301 = ?,btn_302 = ?,btn_303 = ?,btn_401 = ?,btn_402 = ?,btn_501 = ?,btn_601 = ?,btn_602 = ?,btn_603 = ?,btn_604 = ?,btn_605 = ?,btn_701 = ?,btn_702 = ?,btn_801 = ? WHERE startday = ? AND endday = ?";
$bind = array();
$bind = array(intval($clickRow['btn_201']),intval($clickRow['btn_202']),intval($clickRow['btn_203']),intval($clickRow['btn_204']),intval($clickRow['btn_205']),intval($clickRow['btn_207']),intval($clickRow['btn_208']),intval($clickRow['btn_301']),intval($clickRow['btn_302']),intval($clickRow['btn_303']),intval($clickRow['btn_401']),intval($clickRow['btn_402']),intval($clickRow['btn_501']),intval($clickRow['btn_601']),intval($clickRow['btn_602']),intval($clickRow['btn_603']),intval($clickRow['btn_604']),intval($clickRow['btn_605']),intval($clickRow['btn_701']),intval($clickRow['btn_702']),intval($clickRow['btn_801']),$startday,$endday);
$stmt = $tongji_conn->prepare($updateSql);
$stmt->execute($bind);
$stmt = NULL;
 ?>
