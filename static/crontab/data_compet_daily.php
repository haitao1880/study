<?php
date_default_timezone_set('PRC');
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'root','password');
$idc_dsn = 'mysql:host=localhost;port=3306;dbname=rht_idc';
$idc_conn = new PDO($idc_dsn,'root','password');
$account_dsn = 'mysql:host=localhost;port=3306;dbname=rht_idc';
$account_conn = new PDO($account_dsn,'root','password');
$mac_dsn = 'mysql:host=localhost;port=3306;dbname=rht_admin';
$mac_conn = new PDO($mac_dsn,'root','password');
$predate = date('Y-m-d',strtotime('-1 days'));
$tempPredate = date('Ymd',strtotime('-1 days'));
$currentdate = date('Y-m-d');
$premiketime = strtotime($predate);
$curmiketime = strtotime($currentdate)-1;

$view_times = $open_num = $open_times = $first_open = $second_open = $third_open = $fourth_open = $fifth_open = $sixth_open = $seventh_open = $eighth_open = $ninth_open = $tenth_open = $pass_5 = $pass_10 = $pass_15 = $pass_20 = $pass_25 = $pass_30 = $pass_35 = $pass_40 = $pass_45 = $pass_50 = $pass_other = $share_times = $share_ok = $dl_click = $dj_click = $dl_ad_click = $dl_ios_click = $start_dl_times = $new_num = $new_reg = $eleventh_open = $twelfth_open = $thirteenth_open = $fourteenth_open = $fifteenth_open = 0;

$shareSql = '
    SELECT * FROM
	(SELECT count(id) AS total_views FROM rhc_game_platform_match WHERE cday = '.$tempPredate.' AND type = 0 AND pid = 0 AND `from` = 2) AS viewtimes,
    (SELECT count(DISTINCT client) AS total_num FROM rhc_game_platform_match WHERE cday = '.$tempPredate.' AND type = 0 AND pid = 0 AND `from` = 2) AS viewnum,
    (SELECT count(id) AS total_times FROM rhc_game_platform_match WHERE cday = '.$tempPredate.' AND type = 104 AND pid = 0 AND `from` = 2) AS playtimes,
    (SELECT count(DISTINCT mobile) AS total_player FROM rhc_game_platform_match WHERE cday = '.$tempPredate.' AND type = 104 AND pid = 0 AND `from` = 2) AS player,
    (SELECT count(DISTINCT mobile) AS total_pass05 FROM rhc_game_platform_match WHERE cday = '.$tempPredate.' AND type = 101 AND pid > 4  AND `from` = 2) AS pass05,
    (SELECT count(DISTINCT mobile) AS total_pass10 FROM rhc_game_platform_match WHERE cday = '.$tempPredate.' AND type = 101 AND pid > 9  AND `from` = 2) AS pass10,
    (SELECT count(DISTINCT mobile) AS total_pass15 FROM rhc_game_platform_match WHERE cday = '.$tempPredate.' AND type = 101 AND pid > 14  AND `from` = 2) AS pass15,
    (SELECT count(DISTINCT mobile) AS total_pass20 FROM rhc_game_platform_match WHERE cday = '.$tempPredate.' AND type = 101 AND pid > 19  AND `from` = 2) AS pass20,
    (SELECT count(DISTINCT mobile) AS total_pass25 FROM rhc_game_platform_match WHERE cday = '.$tempPredate.' AND type = 101 AND pid > 24  AND `from` = 2) AS pass25,
    (SELECT count(DISTINCT mobile) AS total_pass30 FROM rhc_game_platform_match WHERE cday = '.$tempPredate.' AND type = 101 AND pid > 29  AND `from` = 2) AS pass30,
    (SELECT count(id) AS total_share FROM rhc_game_platform_match WHERE cday = '.$tempPredate.' AND type = 201 AND pid = 0 AND `from` = 2) AS share_times,
    (SELECT count(id) AS total_ok FROM rhc_game_platform_match WHERE cday = '.$tempPredate.' AND type = 202 AND pid = 0 AND `from` = 2) AS share_ok
    ';

$rs = $tongji_conn->query($shareSql);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row = $rs->fetch();
$view_times = intval($row['total_views']);
$open_num = intval($row['total_num']);
$open_times = intval($row['total_times']);
$open_person = intval($row['total_player']);
$pass_5 = intval($row['total_pass05']);
$pass_10 = intval($row['total_pass10']);
$pass_15 = intval($row['total_pass15']);
$pass_20 = intval($row['total_pass20']);
$pass_25 = intval($row['total_pass25']);
$pass_30 = intval($row['total_pass30']);
$pass_35 = 0;
$pass_40 = 0;
$pass_45 = 0;
$pass_50 = 0;
$pass_other = 0;
$share_times = intval($row['total_share']);
$share_ok = intval($row['total_ok']);

$timesql = 'SELECT mobile,count(id) AS total_plays FROM rhc_game_platform_match WHERE cday = "'.$tempPredate.'" AND `from` = 2 AND type = 102 GROUP BY mobile';
$timeRs = $tongji_conn->query($timesql);
$timeRs->setFetchMode(PDO::FETCH_ASSOC);
$timeRows = $timeRs->fetchAll();
foreach($timeRows as $k=>$v){
    if($v['total_plays'] >= 1){$first_open++;}
    if($v['total_plays'] >= 2){$second_open++;}
    if($v['total_plays'] >= 3){$third_open++;}
    if($v['total_plays'] >= 4){$fourth_open++;}
    if($v['total_plays'] >= 5){$fifth_open++;}
    if($v['total_plays'] >= 6){$sixth_open++;}
    if($v['total_plays'] >= 7){$seventh_open++;}
    if($v['total_plays'] >= 8){$eighth_open++;}
    if($v['total_plays'] >= 9){$ninth_open++;}
    if($v['total_plays'] >= 10){$tenth_open++;}
    if($v['total_plays'] >= 11){$eleventh_open++;}
    if($v['total_plays'] >= 12){$twelfth_open++;}
    if($v['total_plays'] >= 13){$thirteenth_open++;}
    if($v['total_plays'] >= 14){$fourteenth_open++;}
    if($v['total_plays'] >= 15){$fifteenth_open++;}
}

//获取前一天 新开启用户 注册用户
$new_num = 0;
$userSql = 'SELECT DISTINCT client FROM rhc_game_platform_match WHERE cday = "'.$tempPredate.'" AND type = 0 AND pid = 0 AND `from` = 2';
$userRs = $tongji_conn->query($userSql);
$userRs->setFetchMode(PDO::FETCH_ASSOC);
$userRows = $userRs->fetchAll();
foreach($userRows as $v){
    $tempSql = 'SELECT id FROM rha_open_trainapp WHERE usermac = "'.strtoupper($v['client']).'" OR usermac = "'.$v['client'].'" limit 1';
    $tempRs = $mac_conn->query($tempSql);
    $tempRs->setFetchMode(PDO::FETCH_ASSOC);
    $tempRow = $tempRs->fetch();
    if(!$tempRow){
        $new_num++;
    }  
}

$regsql = 'SELECT count(id) as total_num FROM rhi_account WHERE trainno = "JJ" AND regtime between '.$premiketime.' AND '.$curmiketime;
$regRs = $account_conn->query($regsql);
$regRs->setFetchMode(PDO::FETCH_ASSOC);
$regRow = $regRs->fetch();
$new_reg = intval($regRow['total_num']);


$dailySql = 'SELECT id FROM rhc_compet_daily WHERE day = "'.$predate.'"';
$dailyRs = $tongji_conn->query($dailySql);
$dailyRs->setFetchMode(PDO::FETCH_ASSOC);
$dailyRow = $dailyRs->fetch();
if($dailyRow){
    $updateSql = "UPDATE rhc_compet_daily SET view_times = ?,open_num = ?,open_times = ?,open_person = ?,first_open = ?,second_open = ?,third_open = ?,fourth_open = ?,fifth_open = ?,sixth_open = ?,seventh_open = ?,eighth_open = ?,ninth_open = ?,tenth_open = ?,pass_5 = ?,pass_10 = ?,pass_15 = ?,pass_20 = ?,pass_25 = ?,pass_30 = ?,pass_35 = ?,pass_40 = ?,pass_45 = ?,pass_50 = ?,pass_other = ?,share_times = ?,share_ok = ?,new_num = ?,new_reg = ?,eleventh_open = ?,twelfth_open = ?,thirteenth_open = ?,fourteenth_open = ?,fifteenth_open = ? WHERE day = ?";
	$bind = array();
	$bind = array($view_times,$open_num,$open_times,$open_person,$first_open,$second_open,$third_open,$fourth_open,$fifth_open,$sixth_open,$seventh_open,$eighth_open,$ninth_open,$tenth_open,$pass_5,$pass_10,$pass_15,$pass_20,$pass_25,$pass_30,$pass_35,$pass_40,$pass_45,$pass_50,$pass_other,$share_times,$share_ok,$new_num,$new_reg,$eleventh_open,$twelfth_open,$thirteenth_open,$fourteenth_open,$fifteenth_open,$predate);
	$stmt = $tongji_conn->prepare($updateSql);
	$stmt->execute($bind);
}else{
    
    $insertSql = "INSERT INTO rhc_compet_daily(view_times,open_num,open_times,open_person,first_open,second_open,third_open,fourth_open,fifth_open,sixth_open,seventh_open,eighth_open,ninth_open,tenth_open,pass_5,pass_10,pass_15,pass_20,pass_25,pass_30,pass_35,pass_40,pass_45,pass_50,pass_other,share_times,share_ok,new_num,new_reg,eleventh_open,twelfth_open,thirteenth_open,fourteenth_open,fifteenth_open,day) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($view_times,$open_num,$open_times,$open_person,$first_open,$second_open,$third_open,$fourth_open,$fifth_open,$sixth_open,$seventh_open,$eighth_open,$ninth_open,$tenth_open,$pass_5,$pass_10,$pass_15,$pass_20,$pass_25,$pass_30,$pass_35,$pass_40,$pass_45,$pass_50,$pass_other,$share_times,$share_ok,$new_num,$new_reg,$eleventh_open,$twelfth_open,$thirteenth_open,$fourteenth_open,$fifteenth_open,$predate);
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind); 
}
$stmt = NULL;


 ?>
