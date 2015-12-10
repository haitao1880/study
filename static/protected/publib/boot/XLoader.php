<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:XLoader.php
* 创建时间:下午5:37:31
* 字符编码:UTF-8
* 版本信息:$Id: XLoader.php 10 2014-06-13 07:03:39Z tony_ren $
* 修改日期:$LastChangedDate: 2014-06-13 15:03:39 +0800 (周五, 13 六月 2014) $
* 最后版本:$LastChangedRevision: 10 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/publib/boot/XLoader.php $
* 摘    要:自动加载类
*/
require_once COMMON_PATH.'XLogger.php';
class XLoader
{
	public static function loader($class_name)
	{
		//exit($class_name);
		
		if (class_exists($class_name, false) || interface_exists($class_name, false))
		{
			return true;
		}
		
		$tmps = explode("_", $class_name);
		$t = end($tmps);
		$t_lower = strtolower($t);
		$t_lower_last = substr($t_lower, -5);
		
		if($t_lower_last == "const")
		{
			$file_name = CONST_PATH.$t_lower.".php";
			if(file_exists($file_name))
			{
				return require_once($file_name);
			}
		}
		
		$curpath = constant(strtoupper($tmps[0]."_PATH"));
		
		if(stripos($class_name,"Controller") !== false)
		{
			$file_name = $curpath."controller".DIRECTORY_SEPARATOR.$t.".php";
			
			if(file_exists($file_name))
			{
				return require_once($file_name);
			}
		}
		
		if(substr($t_lower, -4) == "rule")
		{
			$file_name = $curpath."dbrule".DIRECTORY_SEPARATOR.$class_name.".php";
			if(file_exists($file_name))
			{
				return require_once($file_name);
			}
		}
		if($t_lower_last == "model")
		{
			$file_name = $curpath."models".DIRECTORY_SEPARATOR.$class_name.".php";
			if(file_exists($file_name))
			{
				return require_once($file_name);
			}
		}
		
		if($t_lower_last == "logic")
		{
			$file_name = $curpath."dislogic".DIRECTORY_SEPARATOR.$class_name.".php";
			if(file_exists($file_name))
			{
				return require_once($file_name);
			}
		}
		//echo $file_name;
		//echo "class not find:[$class_name]";
		//JdLogger::getInstance($tmps[0])->error("class not find:[$class_name]");
		
		//return false;
	}
	
	public static function errorHandler($errNo, $errMsg, $errFile, $errLine)
	{
		global $G_X;
	    if (in_array($errNo, array(E_USER_ERROR, E_ERROR))) {	        
	        $msg = "errNO:[".$errNo."] errFile:[".$errFile."] errLine:[".$errLine."]-->".
	        	$errMsg;
	        XLogger::getInstance($G_X['prjstr'])->error($msg);
	        	        
			echo $msg;
			exit;
	    }
	    return true;
	}
	
	public static function exception_handler($exception) {
		global $G_X;
        $msg = $exception->getMessage();
        XLogger::getInstance($G_X['prjstr'])->error($msg);

        //echo $msg;
		//exit;
    }
	
	public static function load() {	
        set_include_path(
			implode(PATH_SEPARATOR, 
				array(
				PUBLIB_PATH,
				COMMON_PATH,
				CONF_PATH,
				CONST_PATH,
				get_include_path()
				)
			)
		);
		
    }
}
?>