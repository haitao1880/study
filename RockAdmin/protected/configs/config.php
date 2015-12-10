<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月4日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:config.php                                                
* 创建时间:下午2:58:21                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: config.php 3174 2014-08-20 01:49:06Z terry $                                                 
* 修改日期:$LastChangedDate: 2014-08-20 09:49:06 +0800 (周三, 20 八月 2014) $                                     
* 最后版本:$LastChangedRevision: 3174 $                                 
* 修 改 者:$LastChangedBy: terry $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/configs/config.php $                                            
* 摘    要:系统配置                                                       
*/
$G_X = array(
	'db' => array(
		'host' =>  '118.244.237.229',
		'dbname' => 'rht_admin',
		'username' => 'terry',
		'password' => 'rockhippoterry',
		'charset' => 'utf8',
		'port' => '3306',
		'persistent' => 'false',
		'tb_prefix' => '',
		'dbtype' => 'mysql'
	),
	'db-rht_idc' => array(
		'host' =>  '192.168.28.201',
		'dbname' => 'rht_idc',
		'username' => 'root',
		'password' => 'password',
		'charset' => 'utf8',
		'port' => '3306',
		'persistent' => 'false',
		'tb_prefix' => '',
		'dbtype' => 'mysql'
	),
	'db-rht_sync' => array(
		'host' =>  'localhost',
		'dbname' => 'rht_sync',
		'username' => 'root',
		'password' => 'password',
		'charset' => 'utf8',
		'port' => '3306',
		'persistent' => 'false',
		'tb_prefix' => '',
		'dbtype' => 'mysql'
	),
	'db-rht_train' => array(
		'host' =>  '192.168.28.201',
		'dbname' => 'rht_train',
		'username' => 'root',
		'password' => 'password',
		'charset' => 'utf8',
		'port' => '3306',
		'persistent' => 'false',
		'tb_prefix' => '',
		'dbtype' => 'mysql'
	),
    'memcacheserver'=>array(
    	"host"=>"localhost",
    	"port"=>11211,
    	"timeout"=>5,
	'prekey'=>''
    ),
    'modstr' => '',//CONTROLLER
    'actstr' => '',
    'prjstr' => '',
    'template' => 'default',//模板信息
    'all_privilege'=>array('url'=>'all'),
    
    
    //订单中的producttype定义
    'order_type'=>array(
	    'food_type'=>array(10,'美食'),
	    'video_type'=>array(1,'视频'),
	    'music_type'=>array(2,'音乐'),
	    'game_type'=>array(3,'游戏'),
	    'app_type'=>array(4,'应用')
    ),
    'email'=>array(
    	'issmtp'=>1,
    	'charset'=>'UTF-8',
    	'host'=>'mail.rockhippo.cn',
    	'username'=>'logs@rockhippo.cn',
    	'password'=>'rockhippo',
    	'port'=>25,
    	'fromname'=>'伙伴'
    	),
    'tableno'=>array(
    	'account'=>1,
		'ads'=>2,
		'album'=>3,
		'albummusic'=>4,
		'appimg'=>5,
		'appkeys'=>6,
		'apps'=>7,
		'foodish'=>8,
		'music'=>9,
	   	'news'=>10 ,
	    'videos'=>11,
	    'trainno'=>12,
	    'trainnodetail'=>13
	)
);

?>
