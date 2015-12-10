<?php
define('ROOT_PATH', dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR);
define('CONF_PATH', ROOT_PATH.'protected\configs'.DIRECTORY_SEPARATOR);
require_once CONF_PATH.'config.php';
$tongji_dsn = 'mysql:host='.$G_X['rht_static']['host'].';port='.$G_X['rht_static']['port'].';dbname='.$G_X['rht_static']['dbname'];
$tongji_conn = new PDO($tongji_dsn,$G_X['rht_static']['username'],$G_X['rht_static']['password']);
$predate = date('Y-m-d',strtotime('-1 days'));
$currentdate = date('Y-m-d');
$premiketime = strtotime($predate);
$curmiketime = strtotime($currentdate)-1;
$premiketime = 0;
//获取前一天 client
$clientSql = 'SELECT DISTINCT client FROM rhc_game_platform WHERE create_time BETWEEN '.$premiketime.' AND '.$curmiketime.' AND type = 1';
$macSql = 'SELECT * FROM rhc_client';
$clientRs = $tongji_conn->query($clientSql);
$clientRs->setFetchMode(PDO::FETCH_ASSOC);
$clientRows = $clientRs->fetchAll();

$macRs = $tongji_conn->query($macSql);
$macRs->setFetchMode(PDO::FETCH_ASSOC);
$macRows = $macRs->fetchAll();
$macArr = array();
foreach($macRows as $v){$macArr[] = $v['client'];}
$insertSql = "INSERT INTO rhc_client(client) VALUES (?)";
//插入新用户的mac地址
foreach($clientRows as $v){
    $bind = array();
    if(!in_array($v['client'],$macArr)){
        $tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bind = array($v['client']);
        $stmt = $tongji_conn->prepare($insertSql);
        $stmt->execute($bind);
    }
}
$stmt = NULL;

 ?>