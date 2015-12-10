<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:XRedis.php
* 创建时间:下午5:34:20
* 字符编码:UTF-8
* 版本信息:$Id: XRedis.php 53 2014-03-27 09:06:56Z tony_ren $
* 修改日期:$LastChangedDate: 2014-03-27 17:06:56 +0800 (周四, 27 三月 2014) $
* 最后版本:$LastChangedRevision: 53 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/web/protected/publib/comm/XRedis.php $
* 摘    要:Redis缓存
*/

class XRedis
{
 	private $host = '127.0.0.1';
 	private $port = 6379;
 	private $timeout = 3;
 	private $redis= null;
 	
 	public function __construct($host,$port,$timeout)
 	{
 		$this->host = $host;
 		$this->port = $port;
 		$this->timeout = $timeout;
 		
 		$this->redis = new Redis();
 		$this->redis->connect($host,$port,$timeout);
 	}
}