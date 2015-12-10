<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月4日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:define.php                                                
* 创建时间:下午2:57:32                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: define.php 2175 2014-08-12 13:19:06Z terry $                                                 
* 修改日期:$LastChangedDate: 2014-08-12 21:19:06 +0800 (周二, 12 八月 2014) $                                     
* 最后版本:$LastChangedRevision: 2175 $                                 
* 修 改 者:$LastChangedBy: terry $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/public/psys/define.php $                                            
* 摘    要:常量定义及时区设置                                                      
*/

//时区定义
ini_set('date.timezone', 'Asia/Shanghai');
ini_set('date.default_latitude', 31.5167);
ini_set('date.default_longitude', 121.4500);
error_reporting(E_ALL);
//

//定义COOKIE域
define("COOKIE_DOMAIN", 'wonaonao.com');
ini_set('session.cookie_domain', COOKIE_DOMAIN);

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH',dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR);
define('PROTECTED_PATH',ROOT_PATH."protected".DIRECTORY_SEPARATOR);
define('DATA_PATH',ROOT_PATH."data".DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', ROOT_PATH."public".DIRECTORY_SEPARATOR);

define('DATA_CACHE_PATH',DATA_PATH."cache".DIRECTORY_SEPARATOR);
define('DATA_LOG_PATH',DATA_PATH."log".DIRECTORY_SEPARATOR);
define('DATA_UPLOAD_PATH',DATA_PATH."upload".DIRECTORY_SEPARATOR);
define('DATA_TEMPLATE_PATH',DATA_PATH."views_c".DIRECTORY_SEPARATOR);

define('PUBLIB_PATH', PROTECTED_PATH."publib".DIRECTORY_SEPARATOR);
define('CONF_PATH', PROTECTED_PATH."configs".DIRECTORY_SEPARATOR);
define('CONST_PATH',PROTECTED_PATH."xconst".DIRECTORY_SEPARATOR);
define('COMMON_PATH', PUBLIB_PATH."comm".DIRECTORY_SEPARATOR);
define('BOOT_PATH', PUBLIB_PATH."boot".DIRECTORY_SEPARATOR);

define('ERRLOG_PATH', '/data/logs/www/');//  /data/logs/www/

//==============================1，网站前台===============================
define("PC_DEBUG", true);
define('PC_PATH', PROTECTED_PATH."module".DIRECTORY_SEPARATOR."pc".DIRECTORY_SEPARATOR);
define("PC_LOG_PATH",DATA_LOG_PATH."pc".DIRECTORY_SEPARATOR);
define('PC_VIEWS_PATH', PC_PATH."views".DIRECTORY_SEPARATOR);
define('PC_VIEWSC_PATH', DATA_TEMPLATE_PATH."pc".DIRECTORY_SEPARATOR);
// define('PWEB_UPLOAD_URL', "/upload/");
define('PC_BASE_URL', 'http://www.wonaonao.com/');

// //==============================2，后台管理===============================
define("PSYS_DEBUG", true);
define('PSYS_PATH', PROTECTED_PATH."module".DIRECTORY_SEPARATOR."psys".DIRECTORY_SEPARATOR);
define("PSYS_LOG_PATH",DATA_LOG_PATH."psys".DIRECTORY_SEPARATOR);
define('PSYS_VIEWS_PATH', PSYS_PATH."views".DIRECTORY_SEPARATOR);
define('PSYS_VIEWSC_PATH', DATA_TEMPLATE_PATH."psys".DIRECTORY_SEPARATOR);
define('PSYS_BASE_URL', "http://admin.rockhippo.com/");
// //==============================3，安装包路径===============================
define('INSTALL',str_replace('\\','/',dirname(dirname(dirname(dirname(__FILE__))))));
define('GAME_PATH',INSTALL.'/traindata/imgs/games/');
define('APP_PATH',INSTALL.'/traindata/imgs/apps/');
// //==============================4，广告图片路径===============================
define('ADDS_PATH',INSTALL.'/traindata_appui/imgs/ppts/');
// //==============================4，行程图片路径===============================
define('TRIPS_PATH',INSTALL.'/traindata/imgs/trip/');
// //==============================5，电影路径与电影封面===============================
define('VIDEO_PATH',INSTALL.'/traindata/imgs/videos/');
// //==============================5，专辑图片路径===============================
define('ALBUM_PATH',INSTALL.'/traindata/imgs/album/');
// //==============================6，虚拟商城===============================
define('GOODS_PATH',INSTALL.'/traindata/imgs/goods/');
//全局常量
include_once CONST_PATH.'globalconst.php';


?>