<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:XLogger.php
* 创建时间:下午5:33:04
* 字符编码:UTF-8
* 版本信息:$Id: XLogger.php 10 2014-06-13 07:03:39Z tony_ren $
* 修改日期:$LastChangedDate: 2014-06-13 15:03:39 +0800 (周五, 13 六月 2014) $
* 最后版本:$LastChangedRevision: 10 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/publib/comm/XLogger.php $
* 摘    要:日志记录封装类
*/
require_once PUBLIB_PATH.'log4php'.DIRECTORY_SEPARATOR.'Logger.php';

class XLogger
{
	protected static $_instance = null;
	protected $_logger = null;
	protected static $_logname = null;
	protected function __construct() 
	{
		
	}
	
	/**
	 * 取得日志类实例
	 * @param string $logname psys,panalyse,pexam,pitem试
	 * //1，系统管理   psys 2，题库试卷  pitem 3，在线测试   pexam 4，考试分析 panalyse
	 * @return object
	 */
	public static function getInstance($logname='pweb')
    {
    	if (null === self::$_logname){self::$_logname = $logname;}
        if (null === self::$_instance) {self::$_instance = new XLogger();}
        if ($logname !== self::$_logname){
        	self::$_logname = $logname;
        	self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    protected function getLogger()
    {
    	if(null == $this->_logger)
    	{
    		$logfile = self::$_logname."log.xml";
    		Logger::configure(CONF_PATH.DIRECTORY_SEPARATOR.$logfile);
			$this->_logger = Logger::getRootLogger();
    	}
    	
    	return $this->_logger;
    }
    
    public function setLogger(LoggerRoot $logger)
    {
    	$this->_logger = $logger;
    }
	
	public function info($msg)
	{
		$this->getLogger()->info($msg);
	}
	
	public function debug($msg)
	{
		$this->getLogger()->debug($msg);
	}
	
	public function error($msg)
	{
		$this->getLogger()->error($msg);
	}
}