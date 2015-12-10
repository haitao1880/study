<?php
define('ROOT_PATH', dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR);
define('CONF_PATH', ROOT_PATH.'protected\configs'.DIRECTORY_SEPARATOR);
require_once CONF_PATH.'config.php';
$tongji_dsn = 'mysql:host='.$G_X['rht_static']['host'].';port='.$G_X['rht_static']['port'].';dbname='.$G_X['rht_static']['dbname'];
$tongji_conn = new PDO($tongji_dsn,$G_X['rht_static']['username'],$G_X['rht_static']['password']);
$premonth = date('Y-m-01',strtotime('-1 month'));

//获取前一月 数据
$gameSql = 'SELECT * FROM rhc_useronline WHERE create_time < '.strtotime($premonth);
$gameRs = $tongji_conn->query($gameSql);
$gameRs->setFetchMode(PDO::FETCH_ASSOC);
$gameRows = $gameRs->fetchAll();

$insertSql = "INSERT INTO rhc_useronline_bak(station_id,appkey,client,wlanip,mobile,cday,create_time,update_time) VALUES (?,?,?,?,?,?,?,?)";
$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
$tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
foreach($gameRows as $v){
    $bind = array($v['station_id'],$v['appkey'],$v['client'],$v['wlanip'],$v['mobile'],$v['cday'],$v['create_time'],$v['update_time']);
    $stmt = $tongji_conn->prepare($insertSql);
    $stmt->execute($bind);
}
$stmt = NULL;
$delSql = "DELETE FROM rhc_useronline where create_time < ".strtotime($premonth);
$tongji_conn->exec($delSql);
 ?>