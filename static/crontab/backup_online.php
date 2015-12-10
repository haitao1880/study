<?php
date_default_timezone_set('PRC');
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'tongji','RN}_IwoAGVYh5xYh]-=-');
$premonth = date("Y-m-d",mktime(0, 0 , 0,date("m")-2,1,date("Y")));

//转移前两月 数据
$onlineSql = 'SELECT * FROM rhc_useronline WHERE create_time < '.strtotime($premonth);
$onlineRs = $tongji_conn->query($onlineSql);
$onlineRs->setFetchMode(PDO::FETCH_ASSOC);
$onlineRows = $onlineRs->fetchAll();

$insertSql = "INSERT INTO rhc_useronline_bak(station_id,appkey,client,wlanip,mobile,cday,create_time,update_time) VALUES (?,?,?,?,?,?,?,?)";
$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
$tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
foreach($onlineRows as $v){
    $bind = array($v['station_id'],$v['appkey'],$v['client'],$v['wlanip'],$v['mobile'],$v['cday'],$v['create_time'],$v['update_time']);
    $stmt = $tongji_conn->prepare($insertSql);
    $stmt->execute($bind);
}
$stmt = NULL;
$delSql = "DELETE FROM rhc_useronline where create_time < ".strtotime($premonth);
$tongji_conn->exec($delSql);
 ?>
