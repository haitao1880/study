<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:XSession.php
* 创建时间:下午5:34:40
* 字符编码:UTF-8
* 版本信息:$Id: XSession.php 10 2014-06-13 07:03:39Z tony_ren $
* 修改日期:$LastChangedDate: 2014-06-13 15:03:39 +0800 (周五, 13 六月 2014) $
* 最后版本:$LastChangedRevision: 10 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/publib/comm/XSession.php $
* 摘    要:session封装
*/
class XSession
{
	static private $_begin = 0;
	static private $_instance = null;
	static private $_debug = false;

	static public function Init($debug=false)
	{
		self::$_instance = new self();
		self::$_debug = $debug;
		
		@session_start();
		session_name('x_'.session_name());		
		
		session_cache_limiter("private");
	}

	static public function Set($name, $v) 
	{
		$_SESSION[$name] = $v;
	}
	
	/**
	 * 閿熸枻鎷烽敓鎴揪鎷烽敓鏂ゆ嫹SESSION閿熷彨纰夋嫹閿熸枻鎷烽敓鎹凤綇鎷烽敓鏂ゆ嫹閿熸枻鎷烽敓娲ヨ繑浼欐嫹NULL
	 * @param $name SESSION閿熷彨纰夋嫹KEY
	 * @param $once 閿熻鍑ゆ嫹浣块敓鏂ゆ嫹涓€閿熻娇纭锋嫹閿熸枻鎷烽敓鏂ゆ嫹
	 * @return mixed
	 */
	static public function Get($name, $once=false)
	{
		$v = null;
		if ( isset($_SESSION[$name]) )
		{
			$v = $_SESSION[$name];
			if ( $once ) unset( $_SESSION[$name] );
		}
		return $v;
	}

	function __construct()
	{
		self::$_begin = microtime(true);
	}

	function __Destruct()
	{
	}
}
?>