<?php
date_default_timezone_set('PRC');
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'tongji','RN}_IwoAGVYh5xYh]-=-');
$premonth = date("Y-m-d",mktime(0, 0 , 0,date("m")-2,1,date("Y")));

//转移前两月 数据
$gameSql = 'SELECT * FROM rhc_game_platform_match WHERE create_time < '.strtotime($premonth);
$gameRs = $tongji_conn->query($gameSql);
$gameRs->setFetchMode(PDO::FETCH_ASSOC);
$gameRows = $gameRs->fetchAll();

$insertSql = "INSERT INTO rhc_game_platform_match_bak(game_id,client,type,pid,from_url,ifweixin,mobile,user_device_info,device_type,real_ip,cday,create_time) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
$tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
foreach($gameRows as $v){
    $bind = array($v['game_id'],$v['client'],$v['type'],$v['pid'],$v['from_url'],$v['ifweixin'],$v['mobile'],$v['user_device_info'],$v['device_type'],$v['real_ip'],$v['cday'],$v['create_time']);
    $stmt = $tongji_conn->prepare($insertSql);
    $stmt->execute($bind);
}
$stmt = NULL;
$delSql = "DELETE FROM rhc_game_platform_match where create_time < ".strtotime($premonth);
$tongji_conn->exec($delSql);
 ?>
