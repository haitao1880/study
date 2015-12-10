<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:XRun.php
* 创建时间:下午5:37:50
* 字符编码:UTF-8
* 版本信息:$Id: XRun.php 53 2014-03-27 09:06:56Z tony_ren $
* 修改日期:$LastChangedDate: 2014-03-27 17:06:56 +0800 (周四, 27 三月 2014) $
* 最后版本:$LastChangedRevision: 53 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/web/protected/publib/boot/XRun.php $
* 摘    要:初始化类
*/
require_once BOOT_PATH.'XLoader.php';
require_once BOOT_PATH.'XBootStrap.php';
class XRun
{
    public static function run($prjname) {
    	global $G_X;
		$G_X['prjstr'] = $prjname;
		
        XLoader::load();
        
        spl_autoload_register(array('XLoader','loader'));
        
        $isdebug = constant(strtoupper($prjname)."_DEBUG");
        $isdebug ? error_reporting(E_ALL ^ E_NOTICE) : error_reporting(0);
        
        set_error_handler(array('XLoader','errorHandler'));
        set_exception_handler(array("XLoader","exception_handler"));
        
		
		$boot = new XBootStrap();
		$boot->run($prjname);
    }
}

?>