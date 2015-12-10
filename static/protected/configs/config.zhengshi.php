<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:config.php
* 创建时间:下午5:00:18
* 字符编码:UTF-8
* 版本信息:$Id: config.php 148 2014-09-18 03:55:15Z terry $
* 修改日期:$LastChangedDate: 2014-09-18 11:55:15 +0800 (周四, 18 九月 2014) $
* 最后版本:$LastChangedRevision: 148 $
* 修 改 者:$LastChangedBy: terry $
* 版本地址:
* 摘    要:系统配置
*/
$G_X = array(
	'db' => array(
		'host' 		=> 'localhost',
		'dbname' 	=> 'rht_tongji',
		'username' 	=> 'tongji',
		'password' 	=> 'RN}_IwoAGVYh5xYh]-=-',
		'charset' 	=> 'utf8',
		'port' 		=> '3306',
		'persistent'=> 'false',
		'tb_prefix' => 'rhc_',
		'dbtype'	=> 'mysql'
	),
    'rht_static' => array(
		'host' 		=> 'localhost',
		'dbname' 	=> 'rht_tongji',
		'username' 	=> 'tongji',
		'password' 	=> 'RN}_IwoAGVYh5xYh]-=-',
		'charset' 	=> 'utf8',
		'port' 		=> '3306',
		'persistent'=> 'false',
		'tb_prefix' => 'rhc_',
		'dbtype'	=> 'mysql'
	),
    'rht_point' => array(
		'host' 		=> 'localhost',
		'dbname' 	=> 'rht_idc',
		'username' 	=> 'root',
		'password' 	=> 'password',
		'charset' 	=> 'utf8',
		'port' 		=> '3306',
		'persistent'=> 'false',
		'tb_prefix' => 'rhi_',
		'dbtype'	=> 'mysql'
	),
    'rht_member' => array(
		'host' 		=> 'localhost',
		'dbname' 	=> 'rht_train',
		'username' 	=> 'root',
		'password' 	=> 'password',
		'charset' 	=> 'utf8',
		'port' 		=> '3306',
		'persistent'=> 'false',
		'tb_prefix' => 'rhi_',
		'dbtype'	=> 'mysql'
	),
    'rha_admin' => array(
		'host' 		=> 'localhost',
		'dbname' 	=> 'rht_admin',
		'username' 	=> 'root',
		'password' 	=> 'password',
		'charset' 	=> 'utf8',
		'port' 		=> '3306',
		'persistent'=> 'false',
		'tb_prefix' => 'rht_',
		'dbtype'	=> 'mysql'
	),
    'memcacheserver'=>array(
    	"host"=>"localhost",
    	"port"=>11211,
    	"timeout"=>5
    ),
    'bus_db' => array(
		'host' =>  'localhost',
		'dbname' => 'rht_bus',
		'username' => 'rockjiaoyun',
		'password' => '9OXTeARSKHI0g',
		'charset' => 'utf8',
		'port' => '3306',
		'persistent' => 'false',
		'tb_prefix' => 'rb_',
		'dbtype' => 'mysql'
	),
    
    'modstr' => '',
    'actstr' => '',
    'prjstr' => '',
    'template' => 'default',
    'all_privilege'=>array('url'=>'All'),
    'rolelist'=>array(),
    'docflow'=>array(),
    'passAddTo'=>'5Afh$6^F'
);

?>
