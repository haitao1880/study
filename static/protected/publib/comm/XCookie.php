<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:XCookie.php
* 创建时间:下午5:31:43
* 字符编码:UTF-8
* 版本信息:$Id: XCookie.php 10 2014-06-13 07:03:39Z tony_ren $
* 修改日期:$LastChangedDate: 2014-06-13 15:03:39 +0800 (周五, 13 六月 2014) $
* 最后版本:$LastChangedRevision: 10 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/publib/comm/XCookie.php $
* 摘    要:COOKIE封装类
*/
class XCookie
{
	static private $_begin = 0;
	static private $_instance = null;
	static private $_debug = false;
    static private $_pre = 'x';
	static public function Init($debug=false)
	{
	}
	
	/**
	 * 获得COOKIE前缀
	 * @return string
	 */
	public static function GetPre()
	{
		return self::$_pre.'_';
	}
	
	
	/**
	 * 设置COOKIE
	 * @param string $k
	 * @param string $v
	 * @param int $expired 秒
	 * @param string $domain
	 */
       // JdCookie::Set($sessionName, $sessionID, 86400);
        //setcookie($sessionName, $sessionID, time() + 1 * 86400, "/",COOKIE_DOMAIN);
	static public function Set($k, $v,$expire = 0,$path = "/",$domain=null) 
	{
		if(empty($domain))$domain=COOKIE_DOMAIN;
		$k = self::$_pre."_".$k;
		if ($expire == 0) {
			$expire = time() + 30 * 86400;
		} else {
                        
			$expire += time();
		}
		if(empty($v))$v='';
		setcookie($k, $v, $expire, $path,$domain);
	}
	
	/**
	 * 获得COOKIE值
	 * @param string $k
	 * @param string $default
	 * @return Ambigous <unknown, string>
	 */
	static public function Get($k, $default = '')
	{
		
		$k = self::$_pre."_".$k;
		
		return isset($_COOKIE[$k]) ? strval($_COOKIE[$k]) : $default;
	}
	
	
	function __construct()
	{
		//self::$_begin = microtime(true);
	}

	function __destruct()
	{
	}
}