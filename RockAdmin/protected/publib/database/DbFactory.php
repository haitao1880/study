<?php
/**
 *	数据库操作类
 *	@author		yqren
 *	@copyright	2011-2012
 *	@version	2.0
 *	@package	PAS2
 *
 *	$Id: DbFactory.php 53 2014-03-27 09:06:56Z tony_ren $
 */
$curdirx = dirname(__FILE__).DIRECTORY_SEPARATOR;
require_once $curdirx.'Pdo'.DIRECTORY_SEPARATOR.'Mysql.php';
require_once $curdirx.'Pdo'.DIRECTORY_SEPARATOR.'Mssql.php';

class DbFactory
{
	protected static $db = null;
	protected static $dbCfg = null;
	public static function Create($configname = 'db')
	{
		//避免重复创建连接
		global $G_X;
		if($G_X[$configname]['dbtype'] == 'mssql')
		{
			$sqltype = 'Pdo_Mssql';
		}else{
			$sqltype = 'Pdo_Mysql';
		}
		
		if (null === self::$dbCfg){self::$dbCfg = $configname;}
        //if (null === self::$db) {self::$db = new Pdo_Mysql($G_X[self::$dbCfg]);}
        if ($configname !== self::$dbCfg){
        	self::$dbCfg = $configname;
        	self::$db = new $sqltype($G_X[self::$dbCfg]);
        }elseif(null === self::$db) {
        	self::$db = new $sqltype($G_X[self::$dbCfg]);
        }
        return self::$db;
	}
	
}
?>