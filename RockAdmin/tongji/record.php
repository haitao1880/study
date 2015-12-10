<?php
/**
 * 用户行为记录
 * @author Neil
 */

include dirname(dirname(dirname(__FILE__))).'/protected/configs/config.php';
include 'define.php';
function real_ip() {
	if (getenv ( 'HTTP_CLIENT_IP' ) && strcasecmp ( getenv ( 'HTTP_CLIENT_IP' ), 'unknown' )) {
		$onlineip = getenv ( 'HTTP_CLIENT_IP' );
	} elseif (getenv ( 'HTTP_X_FORWARDED_FOR' ) && strcasecmp ( getenv ( 'HTTP_X_FORWARDED_FOR' ), 'unknown' )) {
		$onlineip = getenv ( 'HTTP_X_FORWARDED_FOR' );
	} elseif (getenv ( 'REMOTE_ADDR' ) && strcasecmp ( getenv ( 'REMOTE_ADDR' ), 'unknown' )) {
		$onlineip = getenv ( 'REMOTE_ADDR' );
	} elseif (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], 'unknown' )) {
		$onlineip = $_SERVER ['REMOTE_ADDR'];
	}
	preg_match ( "/[\d\.]{7,15}/", $onlineip, $onlineipmatches );
	$onlineip = $onlineipmatches [0] ? $onlineipmatches [0] : 'unknown';
	return $onlineip;
}

error_reporting(E_ALL^E_NOTICE);
WriteLog();
/**
 * 写入日志，统计所需要
 * @author Neil
 */
function WriteLog($return='')
{
	global $G_X;
	session_start();
	$int = real_ip()."[|cut|]";
	$int .= date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME'])."[|cut|]";
	$int .= $G_X['appkey']."[|cut|]";
	$int .= (session_id()?:'-')."[|cut|]";
	$int .= $_SERVER['REQUEST_METHOD']."[|cut|]";
	$int .= "record.php[|cut|]";
	$int .= ($_SERVER['HTTP_REFERER']?:'-')."[|cut|]";
	$int .= ($_SERVER['QUERY_STRING']?:'-')."[|cut|]";
	$int .= $_SERVER['HTTP_USER_AGENT']."[|cut|]";
	$int .= '-' ;
	$int .= PHP_EOL;

	$log = ERRLOG_PATH.'m_wonaonao_record_'.date('H').'.log';
	
	error_log ( $int, 3, $log );
}

?>
