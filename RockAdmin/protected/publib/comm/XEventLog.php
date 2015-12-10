<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:XEventLog.php
* 创建时间:下午5:31:58
* 字符编码:UTF-8
* 版本信息:$Id: XEventLog.php 53 2014-03-27 09:06:56Z tony_ren $
* 修改日期:$LastChangedDate: 2014-03-27 17:06:56 +0800 (周四, 27 三月 2014) $
* 最后版本:$LastChangedRevision: 53 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/web/protected/publib/comm/XEventLog.php $
* 摘    要:记录系统日志
*/
class XEventLog
{
	protected static $tbname = "x_system_log";
	
	public function __construct()
	{
	}
	
	/**
	 * 加入系统日志
	 * @param string $msg 日志内容
	 * @param string $requrl 请求URL
	 * @param array $param 请求参数
	 * @param int $ecode 事件代码 对应configs/eventcode.php 1510
	 * @param int $uid 用户ID
	 * @param int $schoolid 学校ID
	 * @param int $etype 事件类型，1系统管理模块日志
	 */
	//public static function info($msg,$requrl,array $param,$ecode,$uid,$schoolid,$etype=1)
	public static function info($msg,$ecode,$etype,$uid,$schoolid,$requrl,array $param)
	{
		global $G_X;
		
		if($requrl == '')
		{
			$requrl = $_SERVER['REQUEST_URI'];
		}
		if(!$param || count($param) < 1)
		{
			$param = $_REQUEST;
		}
		
		$Update = array();
		$Update['eventtime'] = time();
		$Update['eventtype'] = $etype;
		$Update['schoolid'] = $schoolid;
		$Update['eventdesc'] = $msg;
		$Update['message'] = print_r($param,true);
		$Update['userid'] = $uid;
		$Update['requesturl'] = $requrl;
		$Update['code'] = $ecode;
		$Update['detailcode'] = $G_X['events'][$etype][$ecode];
		$ip = real_ip();
		if($ip == 'unknown')
		{
			$ip = "192.168.0.1";
		}
		$Update['ip'] = ip2long($ip);
				
		require_once PUBLIB_PATH.'database/DbFactory.php';
		$db = DbFactory::Create();
		$id = $db->Insert(self::$tbname,$Update,true);
	}
}
?>