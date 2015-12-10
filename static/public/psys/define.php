<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:define.php
* 创建时间:下午5:18:43
* 字符编码:UTF-8
* 版本信息:$Id: define.php 79 2014-07-08 09:47:07Z jing $
* 修改日期:$LastChangedDate: 2014-07-08 17:47:07 +0800 (周二, 08 七月 2014) $
* 最后版本:$LastChangedRevision: 79 $
* 修 改 者:$LastChangedBy: jing $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/public/psys/define.php $
* 摘    要:常量定义及时区设置
*/

//时区定义
ini_set('date.timezone', 'Asia/Shanghai');
ini_set('date.default_latitude', 31.5167);
ini_set('date.default_longitude', 121.4500);

//定义COOKIE域
define("COOKIE_DOMAIN", 'wonaonao.com');
ini_set('session.cookie_domain', COOKIE_DOMAIN);

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
define('WEB_URL', 'http://adm.wonaonao.com/');
//define('WEB_URL', 'http://114.215.141.72:81/');

//==============================2，后台管理===============================
define("PSYS_DEBUG", true);
define('PSYS_PATH', PROTECTED_PATH."module".DIRECTORY_SEPARATOR."psys".DIRECTORY_SEPARATOR);
define("PSYS_LOG_PATH",DATA_LOG_PATH."psys".DIRECTORY_SEPARATOR);
define('PSYS_VIEWS_PATH', PSYS_PATH."views".DIRECTORY_SEPARATOR);
define('PSYS_VIEWSC_PATH', DATA_TEMPLATE_PATH."psys".DIRECTORY_SEPARATOR);
define('PSYS_OPERATE_PATH', PSYS_PATH."operate".DIRECTORY_SEPARATOR);
// //==============================1，安装包路径===============================
define('INSTALL',str_replace('\\','/',dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
// //==============================2，广告图片路径===============================
define('ADDS_PATH',INSTALL.'/traindata_appui/imgs/ppts/');
// //==============================3，专辑图片路径===============================
define('ALBUM_PATH',INSTALL.'/traindata/imgs/album/');
// //==============================4，电影路径与电影封面===============================
define('VIDEO_PATH',INSTALL.'/traindata/imgs/videos/');

define('PSYS_BASE_URL', "http://adm.wonaonao.com/");
//define('PSYS_BASE_URL', "http://114.215.141.72/");
//全局常量
include_once CONST_PATH.'globalconst.php';

?>
